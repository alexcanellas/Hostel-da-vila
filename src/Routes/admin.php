<?php

declare(strict_types=1);

use App\Controllers\Admin\AcomodacaoAdminController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\DescontoController;
use App\Controllers\Admin\RelatorioController;
use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): void {
    $app->group('/admin', function (RouteCollectorProxy $group): void {

        // Dashboard
        $group->get('/dashboard', [DashboardController::class, 'index']);

        // Acomodações (CRUD completo)
        $group->get('/acomodacoes', [AcomodacaoAdminController::class, 'listar']);
        $group->post('/acomodacoes', [AcomodacaoAdminController::class, 'criar']);
        $group->get('/acomodacoes/{id:[0-9]+}', [AcomodacaoAdminController::class, 'buscar']);
        $group->put('/acomodacoes/{id:[0-9]+}', [AcomodacaoAdminController::class, 'atualizar']);
        $group->delete('/acomodacoes/{id:[0-9]+}', [AcomodacaoAdminController::class, 'remover']);

        // Descontos / cupons
        $group->get('/descontos', [DescontoController::class, 'listar']);
        $group->post('/descontos', [DescontoController::class, 'criar']);
        $group->put('/descontos/{id:[0-9]+}', [DescontoController::class, 'atualizar']);
        $group->delete('/descontos/{id:[0-9]+}', [DescontoController::class, 'remover']);

        // Relatórios
        $group->get('/relatorios/ocupacao', [RelatorioController::class, 'ocupacao']);
        $group->get('/relatorios/financeiro', [RelatorioController::class, 'financeiro']);

    })->add(AuthMiddleware::class);
};
