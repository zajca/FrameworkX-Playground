<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Webmozart\Assert\Assert;

class InternalException extends Exception implements ExceptionInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        string $message = '',
        private ?string $detail = null,
        private array $params = [],
        private ?string $stringCode = null,
        Throwable $previous = null
    ) {
        Assert::stringNotEmpty($message);
        parent::__construct($message, 0, $previous);
    }

    public function title(): string
    {
        return $this->getMessage();
    }

    public function detail(): ?string
    {
        return $this->detail;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function httpStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function stringCode(): ?string
    {
        return $this->stringCode;
    }
}
