<?php

declare(strict_types=1);

namespace App\Request\ArgumentResolver;

use App\Request\ArgumentValueResolverInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Yields a variadic argument's values from the request attributes.
 */
final class VariadicValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ServerRequestInterface $request, ArgumentMetadata $argument): bool
    {
        return $argument->isVariadic() && $request->getAttribute($argument->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ServerRequestInterface $request, ArgumentMetadata $argument): iterable
    {
        $values = $request->getAttribute($argument->getName());

        if (!\is_array($values)) {
            throw new \InvalidArgumentException(sprintf('The action argument "...$%1$s" is required to be an array, the request attribute "%1$s" contains a type of "%2$s" instead.', $argument->getName(), get_debug_type($values)));
        }

        yield from $values;
    }
}
