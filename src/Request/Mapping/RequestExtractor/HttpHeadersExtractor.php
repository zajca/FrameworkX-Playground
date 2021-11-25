<?php

declare(strict_types=1);

namespace App\Request\Mapping\RequestExtractor;

use Psr\Http\Message\ServerRequestInterface;

class HttpHeadersExtractor implements RequestExtractor
{
    public function content(ServerRequestInterface $request): array
    {
        return $request->getHeaders();
    }
}
