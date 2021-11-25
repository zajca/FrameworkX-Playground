<?php

declare(strict_types=1);

namespace App\Bus;

use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class BusFactory
{
    /**
     * @param HandlerDescriptor[][]|callable[][] $handlers
     */
    public function __construct(private array $handlers)
    {
    }

    public function __invoke(): MessageBus
    {
        return new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator($this->handlers)),
        ]);
    }
}
