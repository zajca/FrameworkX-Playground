<?php

declare(strict_types=1);

namespace App\Exception\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ExceptionUrl
{
    public function __construct(
        private string $url
    ) {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \LogicException(sprintf('Exception url must be an url. "%s" given.', $url));
        }
    }

    public function url(): string
    {
        return $this->url;
    }
}
