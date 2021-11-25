<?php

declare(strict_types=1);

namespace App\Request\Mapping;

use App\ToString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidDataException extends \App\Exception\PublicException
{
    private const STRING_CODE = 'request.validationError';
    private const MESSAGE = 'Your request parameters didn\'t validate.';

    /**
     * @var ConstraintViolationListInterface<ConstraintViolationInterface>
     */
    private ConstraintViolationListInterface $violationList;

    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $violationList
     */
    public function __construct(
        ConstraintViolationListInterface $violationList
    ) {
        $this->violationList = $violationList;

        parent::__construct(
            self::MESSAGE,
            null,
            [],
            Response::HTTP_BAD_REQUEST,
            self::STRING_CODE
        );
    }

    public function detail(): string
    {
        $firstViolation = $this->violationList->get(0);

        return (string) $firstViolation->getMessage();
    }

    public function params(): array
    {
        return array_map(
            static fn (ConstraintViolationInterface $violation) => [
                'name' => $violation->getPropertyPath(),
                'reason' => $violation->getMessage(),
                'value' => ToString::toString($violation->getInvalidValue()),
            ], iterator_to_array($this->violationList)
        );
    }

    /**
     * @return ConstraintViolationListInterface<ConstraintViolationInterface>
     */
    public function violationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
