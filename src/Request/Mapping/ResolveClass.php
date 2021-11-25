<?php

declare(strict_types=1);

namespace App\Request\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ResolveClass
{
    /**
     * @param class-string<RequestObjectClassResolver> $classResolver
     * @param class-string                                      $preflightObject
     */
    public function __construct(
        private string $classResolver,
        private string $preflightObject
    ) {
    }

    /**
     * @return class-string<RequestObjectClassResolver>
     */
    public function getClassResolver(): string
    {
        return $this->classResolver;
    }

    /**
     * @return class-string
     */
    public function getPreflightObject(): string
    {
        return $this->preflightObject;
    }
}
