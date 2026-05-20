<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Repositories\PagamentoRepository;
use App\Repositories\ReservaRepository;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class PagamentoService
{
    private Client $http;

    public function __construct(
        private readonly PagamentoRepository $pagamentoRepo,
        private readonly ReservaRepository   $reservaRepo,
        private readonly array               $getnetConfig,
        private readonly LoggerInterface     $logger
    ) {
        $this->http = new Client(['base_uri' => $this->getnetConfig['base_url'], 'timeout' => 30]);
    }

    public function gerarLink(int $reservaId): array
    {
        $reserva = $this->reservaRepo->findById($reservaId);
        if (!$reserva) {
            throw new NotFoundException("Reserva #{$reservaId} não encontrada");
        }

        $token    = $this->autenticarGetnet();
        $linkData = $this->criarLinkGetnet($token, $reserva);

        $pagamentoId = $this->pagamentoRepo->create([
            'reserva_id'     => $reservaId,
            'valor'          => $reserva['preco_final'],
            'status'         => 'pendente',
            'gateway'        => 'getnet',
            'getnet_link_id' => $linkData['payment_link_id'] ?? null,
            'link_pagamento' => $linkData['url'] ?? null,
            'link_expira_em' => date('Y-m-d H:i:s', strtotime('+48 hours')),
        ]);

        $this->logger->info('Link de pagamento gerado', [
            'reserva_id'  => $reservaId,
            'pagamento_id' => $pagamentoId,
        ]);

        return $this->pagamentoRepo->findById($pagamentoId);
    }

    public function processarWebhook(array $payload): void
    {
        $paymentId = $payload['payment_id'] ?? null;
        if (!$paymentId) {
            return;
        }

        $pagamento = $this->pagamentoRepo->findByGetnetPaymentId($paymentId);
        if (!$pagamento) {
            $this->logger->warning('Pagamento Getnet não encontrado', ['payment_id' => $paymentId]);
            return;
        }

        $statusMap = [
            'CONFIRMED' => 'aprovado',
            'DENIED'    => 'recusado',
            'CANCELED'  => 'cancelado',
        ];

        $novoStatus = $statusMap[$payload['status'] ?? ''] ?? null;
        if ($novoStatus) {
            $this->pagamentoRepo->updateStatus($pagamento['id'], $novoStatus, $paymentId, $payload);
        }
    }

    private function autenticarGetnet(): string
    {
        $response = $this->http->post('/auth/oauth/v2/token', [
            'auth'        => [$this->getnetConfig['client_id'], $this->getnetConfig['client_secret']],
            'form_params' => [
                'scope'      => 'oob',
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);
        return $data['access_token'];
    }

    private function criarLinkGetnet(string $token, array $reserva): array
    {
        $response = $this->http->post('/v1/payment-links', [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'seller_id'     => $this->getnetConfig['seller_id'],
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'seller_id'   => $this->getnetConfig['seller_id'],
                'amount'      => (int) ($reserva['preco_final'] * 100),
                'currency'    => 'BRL',
                'description' => "Reserva {$reserva['codigo']} - Hostel da Vila",
                'expiration_date' => date('Y-m-d', strtotime('+48 hours')),
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
