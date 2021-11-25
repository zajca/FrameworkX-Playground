<?php

declare(strict_types=1);

namespace App\Action;

use App\Repository\Test;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Index implements Action
{
    public function __construct(private Test $test)
    {
    }

    public function __invoke(ServerRequestInterface $request): Response
    {
        return new Response(
            200,
            [],
            "Hello wörld!\n" . $this->test->test()
        );
    }
}
