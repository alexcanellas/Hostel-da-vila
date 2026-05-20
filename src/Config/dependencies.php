<?php

declare(strict_types=1);

use App\Repositories\AcomodacaoRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\DescontoRepository;
use App\Repositories\PagamentoRepository;
use App\Repositories\ReservaRepository;
use App\Repositories\UsuarioRepository;
use App\Services\AuthService;
use App\Services\AcomodacaoService;
use App\Services\ReservaService;
use App\Services\PagamentoService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$settings = require __DIR__ . '/settings.php';

return [
    // Configurações
    'settings' => $settings,

    // Logger
    LoggerInterface::class => function (ContainerInterface $c): LoggerInterface {
        $cfg    = $c->get('settings')['log'];
        $logger = new Logger('hosteldavila');
        $level  = Logger::toMonologLevel($cfg['level']);
        $logger->pushHandler(new StreamHandler($cfg['path'], $level));
        return $logger;
    },

    // Conexão PDO
    PDO::class => function (ContainerInterface $c): PDO {
        $db  = $c->get('settings')['database'];
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']};charset={$db['charset']}";
        return new PDO($dsn, $db['username'], $db['password'], $db['options']);
    },

    // Repositories
    UsuarioRepository::class    => fn(ContainerInterface $c) => new UsuarioRepository($c->get(PDO::class)),
    AcomodacaoRepository::class => fn(ContainerInterface $c) => new AcomodacaoRepository($c->get(PDO::class)),
    ClienteRepository::class    => fn(ContainerInterface $c) => new ClienteRepository($c->get(PDO::class)),
    ReservaRepository::class    => fn(ContainerInterface $c) => new ReservaRepository($c->get(PDO::class)),
    PagamentoRepository::class  => fn(ContainerInterface $c) => new PagamentoRepository($c->get(PDO::class)),
    DescontoRepository::class   => fn(ContainerInterface $c) => new DescontoRepository($c->get(PDO::class)),

    // Services
    AuthService::class => fn(ContainerInterface $c) => new AuthService(
        $c->get(UsuarioRepository::class),
        $c->get('settings')['jwt']
    ),

    AcomodacaoService::class => fn(ContainerInterface $c) => new AcomodacaoService(
        $c->get(AcomodacaoRepository::class)
    ),

    ReservaService::class => fn(ContainerInterface $c) => new ReservaService(
        $c->get(ReservaRepository::class),
        $c->get(AcomodacaoRepository::class),
        $c->get(ClienteRepository::class),
        $c->get(DescontoRepository::class),
        $c->get(LoggerInterface::class)
    ),

    PagamentoService::class => fn(ContainerInterface $c) => new PagamentoService(
        $c->get(PagamentoRepository::class),
        $c->get(ReservaRepository::class),
        $c->get('settings')['getnet'],
        $c->get(LoggerInterface::class)
    ),
];
