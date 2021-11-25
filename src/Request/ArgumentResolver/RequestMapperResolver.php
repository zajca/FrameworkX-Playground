<?php

declare(strict_types=1);

namespace App\Request\ArgumentResolver;

use App\Request\ArgumentValueResolverInterface;
use App\Request\Mapping\Attribute\SourceAttribute;
use App\Request\Mapping\RequestMapper;
use App\Request\Mapping\RequestObjectClassResolver;
use App\Request\Mapping\ResolveClass;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestMapperResolver implements ArgumentValueResolverInterface
{
    /**
     * @param RequestMapper  $requestMapper
     * @param ServiceLocator $resolversLocator
     */
    public function __construct(
        private RequestMapper $requestMapper,
        #[TaggedLocator(RequestObjectClassResolver::TAG)] private ContainerInterface $resolversLocator
    ) {
    }

    public function supports(ServerRequestInterface $request, ArgumentMetadata $argument): bool
    {
        // no type and non existent classes should be ignored
        if (
            !is_string($argument->getType())
            || '' === $argument->getType()
            || !(class_exists($argument->getType()) || interface_exists($argument->getType()))
        ) {
            return false;
        }

        // parameter attribute
        if ($argument->getAttributes(SourceAttribute::class, ArgumentMetadata::IS_INSTANCEOF)) {
            return true;
        }

        // class attribute
        $ref = new ReflectionClass($argument->getType());
        $attributes = $ref->getAttributes(SourceAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
        if (1 === count($attributes)) {
            return true;
        }

        // request object class will determinate by resolver
        if (null !== $this->getClassResolverAttribute($argument)) {
            return true;
        }

        return false;
    }

    private function getClassResolverAttribute(
        ArgumentMetadata $argument
    ): ?ResolveClass {
        $attributes = $argument->getAttributes(ResolveClass::class, ArgumentMetadata::IS_INSTANCEOF);
        $nOfAttributes = count($attributes);

        if (0 === $nOfAttributes) {
            return null;
        }
        if (1 === $nOfAttributes) {
            /** @var ResolveClass $attribute */
            $attribute = $attributes[0];

            return $attribute;
        }

        throw new \LogicException(sprintf('More than one "%s" attribute used for argument "%s".', ResolveClass::class, $argument->getName()));
    }

    /**
     * @return \Generator<object>
     */
    public function resolve(ServerRequestInterface $request, ArgumentMetadata $argument): iterable
    {
        /** @var class-string $targetClassToMap */
        $targetClassToMap = $argument->getType();
        $classResolver = $this->getClassResolverAttribute($argument);
        if (null !== $classResolver) {
            if (!$this->resolversLocator->has($classResolver->getClassResolver())) {
                throw new \LogicException(sprintf('Request object class resolver "%s" was not found in container.', $classResolver->getClassResolver()));
            }

            $preflight = $this->requestMapper->mapRequestToObject(
                $request,
                $classResolver->getPreflightObject(),
                null
            );
            /** @var RequestObjectClassResolver $resolver */
            $resolver = $this->resolversLocator->get($classResolver->getClassResolver());
            $targetClassToMap = $resolver->resolve($preflight);
        }

        // check if argument has own source attribute
        /** @var SourceAttribute[] $argumentAttributes */
        $argumentAttributes = $argument->getAttributes(SourceAttribute::class, ArgumentMetadata::IS_INSTANCEOF);
        if (count($argumentAttributes) > 1) {
            throw new \LogicException(sprintf('More than one "%s" attribute used for argument "%s".', SourceAttribute::class, $argument->getName()));
        }
        $dataSourceAttribute = 0 === count($argumentAttributes) ? null : $argumentAttributes[0];
        if ($dataSourceAttribute === null) {
            // if argument has no attribute get it from target class
            $targetClassToMapRef = new ReflectionClass($targetClassToMap);
            $attributes = $targetClassToMapRef->getAttributes(SourceAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
            switch (count($attributes)) {
                case 1:
                    /** @var SourceAttribute $attribute */
                    $attribute = $attributes[0]->newInstance();
                    $dataSourceAttribute = $attribute;
                    break;
                case 0:
                    throw new \LogicException(sprintf('No "%s" attribute used for class "%s".', SourceAttribute::class, $targetClassToMap));
                default:
                    throw new \LogicException(sprintf('More than one "%s" attribute used for class "%s".', SourceAttribute::class, $targetClassToMap));
            }
        }

        yield $this->requestMapper->mapRequestToObject(
            $request,
            $targetClassToMap,
            $dataSourceAttribute
        );
    }
}
