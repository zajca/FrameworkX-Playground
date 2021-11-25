<?php

declare(strict_types=1);

namespace App\Request\Mapping\Attribute;

use App\Request\Mapping\RequestExtractor\RequestExtractor;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [SourceAttribute::TAG])]
interface SourceAttribute
{
    public const TAG = 'zajca.extensions.sourceAttribute';

    public function getSourceName(): ?string;

    /**
     * @return class-string<RequestExtractor>
     */
    public static function getExtractorClass(): string;
}
