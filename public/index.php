<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Carrega variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Monta container de dependências
$containerBuilder = new ContainerBuilder();

if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
    $containerBuilder->enableCompilation(__DIR__ . '/../storage/cache');
}

$containerBuilder->addDefinitions(require __DIR__ . '/../src/Config/dependencies.php');
$container = $containerBuilder->build();

// Cria aplicação
AppFactory::setContainer($container);
$app = AppFactory::create();

// Adiciona middleware base
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

// CORS
$app->add(new App\Middleware\CorsMiddleware());

// Error handler
$errorMiddleware = $app->addErrorMiddleware(
    displayErrorDetails: (bool) ($_ENV['APP_DEBUG'] ?? false),
    logErrors: true,
    logErrorDetails: true,
    logger: $container->get(\Psr\Log\LoggerInterface::class)
);
$errorMiddleware->setDefaultErrorHandler(new App\Middleware\ErrorHandler($app));

// Registra rotas
(require __DIR__ . '/../src/Routes/api.php')($app);
(require __DIR__ . '/../src/Routes/admin.php')($app);
(require __DIR__ . '/../src/Routes/webhooks.php')($app);

$app->run();
