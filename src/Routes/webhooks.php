<?php

declare(strict_types=1);

use App\Controllers\WebhookController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): void {
    $app->group('/webhooks', function (RouteCollectorProxy $group): void {

        // Cloudbeds
        $group->post('/cloudbeds/reserva-criada',    [WebhookController::class, 'cloudbedsReservaCriada']);
        $group->post('/cloudbeds/reserva-modificada', [WebhookController::class, 'cloudbedsReservaModificada']);
        $group->post('/cloudbeds/reserva-cancelada', [WebhookController::class, 'cloudbedsReservaCancelada']);
        $group->post('/cloudbeds/checkin',           [WebhookController::class, 'cloudbedsCheckin']);
        $group->post('/cloudbeds/checkout',          [WebhookController::class, 'cloudbedsCheckout']);

        // Getnet
        $group->post('/getnet/pagamento', [WebhookController::class, 'getnetPagamento']);

        // Kommo CRM
        $group->post('/kommo/lead', [WebhookController::class, 'kommoLead']);

    });
};
