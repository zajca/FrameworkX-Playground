<?php

declare(strict_types=1);

namespace App\Action\Resolver\Request;

use App\Request\Mapping\RawDataValidation;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class QueryRawValidation implements RawDataValidation
{
    private bool $isPublic;

    public static function getConstraint(): array
    {
        return [
            new Collection([
                'isPublic' => [
                    new NotBlank(),
                    new Choice(['0', '1']),
                ],
            ]),
        ];
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }
}
