<?php

declare(strict_types=1);

namespace App\Request\Mapping\RequestExtractor;

use Psr\Http\Message\ServerRequestInterface;

class RouteParametersExtractor implements RequestExtractor
{
    public function content(ServerRequestInterface $request): array
    {
        $params = $request->getAttributes();

        foreach ($params as $key => $param) {
            $value = filter_var($param, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $params[$key] = $value ?? $param;
        }

        return $params;
    }
}
