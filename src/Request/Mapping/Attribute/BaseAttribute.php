<?php

declare(strict_types=1);

namespace App\Request\Mapping\Attribute;

abstract class BaseAttribute implements SourceAttribute
{
    public function __construct(
        private ?string $sourceName = null
    ) {
    }

    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }
}
