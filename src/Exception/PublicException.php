<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Webmozart\Assert\Assert;

class PublicException extends Exception implements ExceptionInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        string $title,
        private ?string $detail = null,
        private array $params = [],
        private int $httpStatusCode = Response::HTTP_BAD_REQUEST,
        private ?string $stringCode = null,
        Throwable $previous = null
    ) {
        Assert::stringNotEmpty($title);
        parent::__construct($title, 0, $previous);
    }

    public function title(): string
    {
        return $this->getMessage();
    }

    public function detail(): ?string
    {
        return $this->detail;
    }

    /**
     * @return array<string, mixed>
     */
    public function params(): array
    {
        return $this->params;
    }

    public function stringCode(): ?string
    {
        return $this->stringCode;
    }

    public function httpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
