<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Action\Action;
use App\Log\Logger;
use App\Request\ArgumentValueResolverInterface;
use App\Request\ArgumentValueResolverMiddleware;
use App\SerializerFactory;
use App\Response\ResponseMiddleware;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

return static function(ContainerConfigurator $configurator) {
    $configurator->parameters()
        ->set('kernel.cache_dir', __DIR__.'/../var/cache')
        ->set('validator.translation_domain', 'validators')
        ->set('email_validation_mode', 'html5')
        ->set('kernel.charset', 'UTF-8')
        ->set('kernel.debug', false);
    $configurator->import(__DIR__.'/../vendor/symfony/framework-bundle/Resources/config/cache.php');
    $configurator->import(__DIR__.'/../vendor/symfony/framework-bundle/Resources/config/validator.php');

    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->instanceof(Action::class)
        ->public();

    $services
        ->instanceof(ArgumentValueResolverInterface::class)
        ->tag('controller.argument_value_resolver');

    $services->load('App\\', '../src/*');

    $services->set(ResponseMiddleware::class)->public();
    $services->set(ArgumentValueResolverMiddleware::class)->public();
    $services->alias(LoggerInterface::class, Logger::class);

    $services->set(SerializerInterface::class)->factory(service(SerializerFactory::class));
    $services->alias(DenormalizerInterface::class, SerializerInterface::class);
};
