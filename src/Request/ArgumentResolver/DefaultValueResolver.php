<?php

declare(strict_types=1);

namespace App\Request\ArgumentResolver;

use App\Request\ArgumentValueResolverInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Yields the default value defined in the action signature when no value has been given.
 */
final class DefaultValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ServerRequestInterface $request, ArgumentMetadata $argument): bool
    {
        return $argument->hasDefaultValue() || (null !== $argument->getType() && $argument->isNullable() && !$argument->isVariadic());
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ServerRequestInterface $request, ArgumentMetadata $argument): iterable
    {
        yield $argument->hasDefaultValue() ? $argument->getDefaultValue() : null;
    }
}
