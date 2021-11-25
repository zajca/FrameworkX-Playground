<?php

declare(strict_types=1);

namespace App\Action\Resolver\Request;

use App\BoolUtils;
use App\Request\Mapping\ManualDataMapping;
use App\Request\Mapping\RawDataValidation;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class QueryManualMapping implements RawDataValidation, ManualDataMapping
{
    private bool $isPublic;

    public function __construct(bool $isPublic)
    {
        $this->isPublic = $isPublic;
    }

    public static function getConstraint(): array
    {
        return [
            new Collection([
                'isPublic' => [
                    new NotBlank(),
                    new Choice(['0', '1', 'false', 'true']),
                ],
            ]),
        ];
    }

    public static function mapFromRequestData(array $data): object
    {
        return new self(BoolUtils::castToBool($data['isPublic']));
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }
}
