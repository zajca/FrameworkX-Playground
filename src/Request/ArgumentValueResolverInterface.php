<?php

declare(strict_types=1);

namespace App\Request;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Responsible for resolving the value of an argument based on its metadata.
 */
interface ArgumentValueResolverInterface
{
    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     */
    public function supports(ServerRequestInterface $request, ArgumentMetadata $argument): bool;

    /**
     * Returns the possible value(s).
     */
    public function resolve(ServerRequestInterface $request, ArgumentMetadata $argument): iterable;
}
