<?php

declare(strict_types=1);

namespace App\Action\Exception;

use App\Action\Action;
use App\Repository\Test;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Internal implements Action
{
    public function __invoke(ServerRequestInterface $request): Response
    {
        throw new \LogicException('I\'m internal exception.');
    }
}
