<?php

declare(strict_types=1);

namespace App\Action\Exception;

use App\Action\Action;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class PublicException implements Action
{
    public function __invoke(ServerRequestInterface $request): Response
    {
        throw new \App\Exception\PublicException(
            'This is public exception',
            'This is detail',
            [
                'param' => 1
            ],
            \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST,
            'exception.public'
        );
    }
}
