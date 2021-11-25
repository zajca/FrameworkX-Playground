<?php

use App\Request\ArgumentValueResolverMiddleware;
use App\Response\ResponseMiddleware;
use React\EventLoop\Loop;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Parameter;

require __DIR__ . '/../vendor/autoload.php';

$file = __DIR__ . '/../var/cache/container.php';
//if (file_exists($file)) {
//    require_once $file;
//    $container = new ProjectServiceContainer();
//} else {
    $container = new ContainerBuilder();
    $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
    $loader->load('services.php');
    $container->addCompilerPass(new \Symfony\Component\Messenger\DependencyInjection\MessengerPass());
    $container->addCompilerPass(new \Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass());

    $version = new Parameter('container.build_id');
    $container->getDefinition('cache.adapter.system')->replaceArgument(2, $version);
    $definition = $container->findDefinition('validator.email');
    $definition->replaceArgument(0, 'html5');

    $container->compile();
    $dumper = new PhpDumper($container);
    file_put_contents($file, $dumper->dump());
//}

$app = new FrameworkX\App(
    Loop::get(),
    $container->get(ResponseMiddleware::class)
);
$app->get('/', $container->get(\App\Action\Index::class));
$app->get('/resolver', $container->get(ArgumentValueResolverMiddleware::class), $container->get(\App\Action\Resolver\Resolver::class));
$app->get('/exception/public', $container->get(\App\Action\Exception\PublicException::class));
$app->get('/exception/internal', $container->get(\App\Action\Exception\Internal::class));

$app->run();
