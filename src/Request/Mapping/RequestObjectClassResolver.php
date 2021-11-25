<?php

declare(strict_types=1);

namespace App\Request\Mapping;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [RequestObjectClassResolver::TAG])]
interface RequestObjectClassResolver
{
    public const TAG = 'zajca.extensions.requestObjectClassResolver';

    /**
     * @return class-string
     */
    public function resolve(object $preflight): string;
}
