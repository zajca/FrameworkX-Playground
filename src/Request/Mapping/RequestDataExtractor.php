<?php

declare(strict_types=1);

namespace App\Request\Mapping;

use App\Request\Mapping\Attribute\SourceAttribute;
use App\Request\Mapping\RequestExtractor\RequestExtractor;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class RequestDataExtractor
{
    /**
     * @param ServiceLocator $extractors
     */
    public function __construct(
        #[TaggedLocator(RequestExtractor::TAG)] private ContainerInterface $extractors
    ) {
    }

    /**
     * @param class-string $class
     *
     * @return array<mixed>
     */
    public function getDataForRequestObjectFromRequest(
        ServerRequestInterface $request,
        SourceAttribute $dataSourceAttribute
    ): array {
        /** @var RequestExtractor $extractor */
        $extractor = $this->extractors->get($dataSourceAttribute::getExtractorClass());
        return $extractor->content($request);
    }
}
