<?php

declare(strict_types=1);

namespace App\Request;

use App\Request\ArgumentResolver\RequestMapperResolver;
use Psr\Http\Message\ServerRequestInterface;
use App\Request\ArgumentResolver\DefaultValueResolver;
use App\Request\ArgumentResolver\RequestAttributeValueResolver;
use App\Request\ArgumentResolver\RequestValueResolver;
use App\Request\ArgumentResolver\VariadicValueResolver;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactoryInterface;

class ArgumentValueResolverMiddleware
{
    private ArgumentMetadataFactoryInterface $argumentMetadataFactory;

    private iterable $argumentValueResolvers;

    /**
     * @param iterable<mixed, ArgumentValueResolverInterface> $argumentValueResolvers
     */
    public function __construct(
        ArgumentMetadataFactoryInterface $argumentMetadataFactory = null,
        #[TaggedIterator('controller.argument_value_resolver')] iterable $argumentValueResolvers = []
    ) {
        $this->argumentMetadataFactory = $argumentMetadataFactory ?? new ArgumentMetadataFactory();
        $this->argumentValueResolvers = $argumentValueResolvers ?: self::getDefaultArgumentValueResolvers();
    }

    /**
     * @return iterable<int, ArgumentValueResolverInterface>
     */
    public static function getDefaultArgumentValueResolvers(): iterable
    {
        return [
            new RequestAttributeValueResolver(),
            new RequestValueResolver(),
            new DefaultValueResolver(),
            new VariadicValueResolver(),
        ];
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $arguments = $this->getArguments($request, $next);

        return $next(...$arguments);
    }

    private function getArguments(ServerRequestInterface $request, callable $controller): array
    {
        $arguments = [];
        foreach ($this->argumentMetadataFactory->createArgumentMetadata($controller) as $metadata) {
            foreach ($this->argumentValueResolvers as $resolver) {
                if (!$resolver->supports($request, $metadata)) {
                    continue;
                }

                $resolved = $resolver->resolve($request, $metadata);

                $atLeastOne = false;
                foreach ($resolved as $append) {
                    $atLeastOne = true;
                    $arguments[] = $append;
                }

                if (!$atLeastOne) {
                    throw new \InvalidArgumentException(sprintf('"%s::resolve()" must yield at least one value.', get_debug_type($resolver)));
                }

                // continue to the next controller argument
                continue 2;
            }

            $representative = $controller;

            if (\is_array($representative)) {
                $representative = sprintf('%s::%s()', \get_class($representative[0]), $representative[1]);
            } elseif (\is_object($representative)) {
                $representative = \get_class($representative);
            }

            throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument. Either the argument is nullable and no null value has been provided, no default value has been provided or because there is a non optional argument after this one.', $representative, $metadata->getName()));
        }

        return $arguments;
    }
}
