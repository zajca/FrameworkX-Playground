<?php

declare(strict_types=1);

namespace App\Request\Mapping\Attribute;

use App\Request\Mapping\RequestExtractor\JsonPayloadExtractor;
use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class JsonPayload extends BaseAttribute
{
    public static function getExtractorClass(): string
    {
        return JsonPayloadExtractor::class;
    }
}
