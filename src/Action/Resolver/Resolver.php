<?php

declare(strict_types=1);

namespace App\Action\Resolver;

use App\Action\Action;
use App\Action\Resolver\Request\Query;
use App\Action\Resolver\Request\QueryManualMapping;
use App\Action\Resolver\Request\QueryRawValidation;
use App\Repository\Test;
use App\Request\Mapping\Attribute\QueryString;

class Resolver implements Action
{
    public function __construct(private Test $test)
    {
    }

    public function __invoke(
        #[QueryString] Query $query,
        #[QueryString] QueryRawValidation $queryRawValidation,
        #[QueryString] QueryManualMapping $queryManualMapping
    ): array {
        return [
            ["Hello wörld!"],
            ['DI result: ' . $this->test->test()],
            ['Query attr validation: ' . $query->isPublic()],
            ['Query raw validation: ' . $queryRawValidation->isPublic()],
            ['Query manual mapping: ' . $queryManualMapping->isPublic()],
        ];
    }
}
