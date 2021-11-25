<?php

declare(strict_types=1);

namespace App\Request\Mapping\RequestExtractor;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [RequestExtractor::TAG])]
interface RequestExtractor
{
    public const TAG = 'zajca.extenstions.requestExtractor';

    /**
     * @return array<string, mixed>
     */
    public function content(ServerRequestInterface $request): array;
}
