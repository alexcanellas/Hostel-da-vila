<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ReservaRepository;
use App\Services\PagamentoService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class WebhookController extends BaseController
{
    public function __construct(
        private readonly PagamentoService  $pagamentoService,
        private readonly ReservaRepository $reservaRepo,
        private readonly LoggerInterface   $logger
    ) {}

    // --- Cloudbeds ---

    public function cloudbedsReservaCriada(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $payload = (array) $request->getParsedBody();
        $this->logger->info('Cloudbeds: reserva criada', $payload);
        // TODO: sincronizar reserva do Cloudbeds com banco local
        return $this->json($response, ['received' => true]);
    }

    public function cloudbedsReservaModificada(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $payload = (array) $request->getParsedBody();
        $this->logger->info('Cloudbeds: reserva modificada', $payload);
        return $this->json($response, ['received' => true]);
    }

    public function cloudbedsReservaCancelada(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $payload = (array) $request->getParsedBody();
        $this->logger->info('Cloudbeds: reserva cancelada', $payload);

        $cbId = $payload['reservation_id'] ?? null;
        if ($cbId) {
            // Busca reserva local pelo ID do Cloudbeds e cancela
            // Implementação completa na fase 2
        }

        return $this->json($response, ['received' => true]);
    }

    public function cloudbedsCheckin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $payload = (array) $request->getParsedBody();
        $this->logger->info('Cloudbeds: check-in', $payload);
        return $this->json($response, ['received' => true]);
    }

    public function cloudbedsCheckout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $payload = (array) $request->getParsedBody();
        $this->logger->info('Cloudbeds: check-out', $payload);
        return $this->json($response, ['received' => true]);
    }

    // --- Getnet ---

    public function getnetPagamento(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $payload = (array) $request->getParsedBody();
        $this->logger->info('Getnet: webhook pagamento', $payload);

        $this->pagamentoService->processarWebhook($payload);

        return $this->json($response, ['received' => true]);
    }

    // --- Kommo ---

    public function kommoLead(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $payload = (array) $request->getParsedBody();
        $this->logger->info('Kommo: webhook lead', $payload);
        // TODO: processar atualização de lead/negócio do Kommo
        return $this->json($response, ['received' => true]);
    }
}
