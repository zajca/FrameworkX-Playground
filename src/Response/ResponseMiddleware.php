<?php

declare(strict_types=1);

namespace App\Response;

use App\Exception\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseMiddleware
{
    public function __construct(private ExceptionHandler $exceptionHandler, private SerializerInterface $serializer)
    {
    }

    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            return $this->exceptionHandler->handleException($e);
        }
        if (!$response instanceof ResponseInterface) {
            $response = new Response(
                200,
                [],
                $this->serializer->serialize($response, 'json')
            );
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
