<?php

declare(strict_types=1);

namespace App\Request\ArgumentResolver;

use App\Request\ArgumentValueResolverInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Yields the same instance as the request object passed along.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class RequestValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ServerRequestInterface $request, ArgumentMetadata $argument): bool
    {
        return ServerRequestInterface::class === $argument->getType() || is_subclass_of($argument->getType(), ServerRequestInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ServerRequestInterface $request, ArgumentMetadata $argument): iterable
    {
        yield $request;
    }
}
