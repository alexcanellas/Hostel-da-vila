<?php

declare(strict_types=1);

namespace App\Repositories;

class PagamentoRepository extends BaseRepository
{
    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM pagamentos WHERE id = ?', [$id]);
    }

    public function findByReservaId(int $reservaId): array
    {
        return $this->fetchAll('SELECT * FROM pagamentos WHERE reserva_id = ? ORDER BY criado_em DESC', [$reservaId]);
    }

    public function findByGetnetPaymentId(string $paymentId): ?array
    {
        return $this->fetchOne('SELECT * FROM pagamentos WHERE getnet_payment_id = ?', [$paymentId]);
    }

    public function create(array $dados): int
    {
        return $this->insert(
            'INSERT INTO pagamentos (reserva_id, valor, status, gateway, getnet_link_id, link_pagamento, link_expira_em)
             VALUES (:reserva_id, :valor, :status, :gateway, :getnet_link_id, :link_pagamento, :link_expira_em)',
            [
                'reserva_id'     => $dados['reserva_id'],
                'valor'          => $dados['valor'],
                'status'         => $dados['status'] ?? 'pendente',
                'gateway'        => $dados['gateway'] ?? 'getnet',
                'getnet_link_id' => $dados['getnet_link_id'] ?? null,
                'link_pagamento' => $dados['link_pagamento'] ?? null,
                'link_expira_em' => $dados['link_expira_em'] ?? null,
            ]
        );
    }

    public function updateStatus(int $id, string $status, ?string $paymentId = null, ?array $dadosGateway = null): bool
    {
        return $this->execute(
            'UPDATE pagamentos
             SET status = :status,
                 getnet_payment_id = COALESCE(:payment_id, getnet_payment_id),
                 dados_gateway = COALESCE(:dados, dados_gateway),
                 pago_em = IF(:status2 = \'aprovado\', NOW(), pago_em)
             WHERE id = :id',
            [
                'id'         => $id,
                'status'     => $status,
                'status2'    => $status,
                'payment_id' => $paymentId,
                'dados'      => $dadosGateway ? json_encode($dadosGateway) : null,
            ]
        ) > 0;
    }
}
