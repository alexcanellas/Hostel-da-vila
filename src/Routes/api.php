<?php

declare(strict_types=1);

use App\Controllers\Api\AcomodacaoController;
use App\Controllers\Api\AuthController;
use App\Controllers\Api\ClienteController;
use App\Controllers\Api\PagamentoController;
use App\Controllers\Api\ReservaController;
use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): void {
    $app->group('/api/v1', function (RouteCollectorProxy $group): void {

        // Health check
        $group->get('/health', function ($req, $res) {
            $res->getBody()->write(json_encode(['status' => 'ok', 'timestamp' => date('c')]));
            return $res->withHeader('Content-Type', 'application/json');
        });

        // Auth
        $group->post('/auth/login', [AuthController::class, 'login']);

        // Acomodações — rotas públicas
        $group->get('/acomodacoes', [AcomodacaoController::class, 'listar']);
        $group->get('/acomodacoes/{id:[0-9]+}', [AcomodacaoController::class, 'buscar']);
        $group->get('/acomodacoes/{id:[0-9]+}/disponibilidade', [AcomodacaoController::class, 'disponibilidade']);

        // Reservas — criação pública
        $group->post('/reservas', [ReservaController::class, 'criar']);

        // Rotas autenticadas (JWT)
        $group->group('', function (RouteCollectorProxy $auth): void {

            // Auth
            $auth->post('/auth/logout', [AuthController::class, 'logout']);
            $auth->get('/auth/me', [AuthController::class, 'me']);

            // Reservas
            $auth->get('/reservas', [ReservaController::class, 'listar']);
            $auth->get('/reservas/{id:[0-9]+}', [ReservaController::class, 'buscar']);
            $auth->put('/reservas/{id:[0-9]+}', [ReservaController::class, 'atualizar']);
            $auth->delete('/reservas/{id:[0-9]+}', [ReservaController::class, 'cancelar']);

            // Clientes
            $auth->get('/clientes', [ClienteController::class, 'listar']);
            $auth->get('/clientes/{id:[0-9]+}', [ClienteController::class, 'buscar']);

            // Pagamentos
            $auth->post('/pagamentos/gerar-link', [PagamentoController::class, 'gerarLink']);
            $auth->get('/pagamentos/{id:[0-9]+}', [PagamentoController::class, 'buscar']);

        })->add(AuthMiddleware::class);

    });
};
