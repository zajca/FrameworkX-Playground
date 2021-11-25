<?php

declare(strict_types=1);

namespace App\Request\ArgumentResolver;

use App\Request\ArgumentValueResolverInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Yields a non-variadic argument's value from the request attributes.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class RequestAttributeValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ServerRequestInterface $request, ArgumentMetadata $argument): bool
    {
        return !$argument->isVariadic() && $request->getAttribute($argument->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ServerRequestInterface $request, ArgumentMetadata $argument): iterable
    {
        yield $request->getAttribute($argument->getName());
    }
}
