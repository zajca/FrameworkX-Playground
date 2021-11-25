<?php

declare(strict_types=1);

namespace App\Action\Resolver\Request;

use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[Valid]
class Query
{
    #[NotNull]
    #[NotBlank]
    #[Type('bool')]
    private ?bool $isPublic = null;

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }
}
