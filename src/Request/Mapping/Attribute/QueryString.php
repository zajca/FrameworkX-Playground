<?php

declare(strict_types=1);

namespace App\Request\Mapping\Attribute;

use App\Request\Mapping\RequestExtractor\QueryStringExtractor;
use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS)]
class QueryString extends BaseAttribute
{
    public static function getExtractorClass(): string
    {
        return QueryStringExtractor::class;
    }
}
