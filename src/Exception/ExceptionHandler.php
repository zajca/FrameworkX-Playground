<?php

declare(strict_types=1);

namespace App\Exception;

use App\Exception\Attribute\ExceptionUrl;
use Psr\Log\LoggerInterface;
use React\Http\Message\Response;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Uid\Uuid;

class ExceptionHandler
{
    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $logger
    ) {
    }

    public function handleException(\Throwable $exception): Response
    {
        $message = $exception->getMessage();

        $statusCode = \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR;
        $headers = [];
        $isInternalError = true;
        if ($exception instanceof HttpExceptionInterface) {
            $isInternalError = false;
            $statusCode = $exception->getStatusCode();
            $headers = $exception->getHeaders();
        }
        if (u($message)->isEmpty()) {
            $message = \array_key_exists($statusCode, \Symfony\Component\HttpFoundation\Response::$statusTexts) ? \Symfony\Component\HttpFoundation\Response::$statusTexts[$statusCode] : 'error';
        }

        $responseData = [
            'status' => $statusCode,
            'title' => $message,
            'exceptionId' => (Uuid::v4())->toRfc4122(),
        ];

        if ($exception instanceof ExceptionInterface) {
            // return form validation exceptions
            $statusCode = $exception->httpStatusCode();
            $responseData['title'] = $exception->title();
            if (null !== $exception->detail()) {
                $responseData['detail'] = $exception->detail();
            }
            $responseData['params'] = $exception->params();
            $responseData['stringCode'] = $exception->stringCode();
            $responseData['status'] = $statusCode;
        }

        if ($exception instanceof PublicException) {
            $isInternalError = false;
        }

        var_export($exception->getMessage());
        if ($isInternalError) {
            //if ($this->env->isDev()) {
                $responseData['stackTrace'] = $exception->getTraceAsString();
            //}
            $this->logger->critical($responseData['title'], $responseData);
            //if (!$this->env->isDev()) {
                $responseData['title'] = 'Internal error.';
                $responseData['detail'] = 'Please contact support.';
            //}
        }

        $ref = new ReflectionClass($exception);

        /** @var array<ReflectionAttribute> $attributes */
        $attributes = $ref->getAttributes(ExceptionUrl::class, ReflectionAttribute::IS_INSTANCEOF);
        if (1 === count($attributes)) {
            /** @var ExceptionUrl $attribute */
            $attribute = $attributes[0]->newInstance();
            $responseData['url'] = $attribute->url();
        }

        $json = $this->serializer->serialize($responseData, 'json');
        $headers['Content-Type'] = 'application/problem+json';
        return new Response($statusCode, $headers, $json);
    }
}
