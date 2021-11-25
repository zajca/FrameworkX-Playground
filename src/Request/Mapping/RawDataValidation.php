<?php

declare(strict_types=1);

namespace App\Request\Mapping;

use Symfony\Component\Validator\Constraint;

interface RawDataValidation
{
    /**
     * @return Constraint[]
     */
    public static function getConstraint(): array;
}
