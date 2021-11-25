<?php

declare(strict_types=1);

namespace App\Bus;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;

class BusCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $routing = [];
        foreach ($container->getDefinitions() as $id => $definition) {
            try {
                $ref = new \ReflectionClass($definition->getClass());
            } catch (\ReflectionException $e) {
                continue;
            }

            if ($ref->implementsInterface(Handler::class)) {
                $invoke = $ref->getMethod('__invoke');
                if (count($invoke->getParameters()) !== 1) {
                    throw new \LogicException(sprintf(
                        'Method __invoke of messenger handler "%s" is expected to have 1 parameter.',
                        $definition->getClass()
                    ));
                }
                $messageClass = $invoke->getParameters()[0]->getType();
                try {
                    new \ReflectionClass($messageClass);
                } catch (\ReflectionException $e) {
                    throw new \LogicException(sprintf(
                        'Method __invoke of messenger handler "%s" parameter must be class.',
                        $definition->getClass()
                    ));
                }

                $routing[$messageClass] = $container->get($id);
            }

            $container->getDefinition(BusFactory::class)->addArgument($routing);
        }
    }
}
