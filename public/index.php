<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../', '.env');
$dotenv->load();

try {

    $builder = new ContainerBuilder();
    $builder->addDefinitions(require __DIR__ . '/../src/Config/dependencies.php');

    $container = $builder->build();

    $app = AppFactory::createFromContainer($container);
    $app->addBodyParsingMiddleware();

    (require __DIR__ . '/../src/Config/cors.php')($app);
    $app->addRoutingMiddleware();

    (require __DIR__ . '/../src/Config/middleware.php')($app, $container);
    (require __DIR__ . '/../src/Config/routes.php')($app);
    (require __DIR__ . '/../src/Config/eloquent.php')($app);

    $app->run();
} catch (Exception $e) {}
