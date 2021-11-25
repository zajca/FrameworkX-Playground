<?php

declare(strict_types=1);

namespace App\Request\Mapping\RequestExtractor;

use JsonException;
use Psr\Http\Message\ServerRequestInterface;

class JsonPayloadExtractor implements RequestExtractor
{
    private const MAX_JSON_DEPTH = 512;

    public function content(ServerRequestInterface $request): array
    {

        $content = (string) $request->getBody();
        if ('' === $content) {
            return [];
        }

        try {
            $data = json_decode($content, true, self::MAX_JSON_DEPTH, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            throw JsonPayloadException::createInvalidPayload($ex);
        }

        if (null === $data) {
            throw JsonPayloadException::createEmptyBody();
        }

        return $data;
    }
}
