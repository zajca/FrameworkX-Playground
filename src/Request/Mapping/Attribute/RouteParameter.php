<?php

declare(strict_types=1);

namespace App\Request\Mapping\Attribute;

use App\Request\Mapping\RequestExtractor\RouteParametersExtractor;
use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class RouteParameter extends BaseAttribute
{
    public static function getExtractorClass(): string
    {
        return RouteParametersExtractor::class;
    }
}
