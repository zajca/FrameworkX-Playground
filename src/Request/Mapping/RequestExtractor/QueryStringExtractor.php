<?php

declare(strict_types=1);

namespace App\Request\Mapping\RequestExtractor;

use Psr\Http\Message\ServerRequestInterface;

class QueryStringExtractor implements RequestExtractor
{
    public function content(ServerRequestInterface $request): array
    {
        return $request->getQueryParams();
    }
}
