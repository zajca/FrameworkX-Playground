<?php

declare(strict_types=1);

namespace App\Request\Mapping;

interface ManualDataMapping
{
    /**
     * @param array<mixed> $data
     */
    public static function mapFromRequestData(array $data): object;
}
