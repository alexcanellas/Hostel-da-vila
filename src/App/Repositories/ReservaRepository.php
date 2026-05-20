<?php

declare(strict_types=1);

namespace App\Repositories;

class ReservaRepository extends BaseRepository
{
    public function findAll(array $filtros = [], int $limit = 20, int $offset = 0): array
    {
        [$where, $params] = $this->buildFiltros($filtros);
        return $this->fetchAll(
            "SELECT r.*, c.nome AS cliente_nome, c.email AS cliente_email,
                    a.nome AS acomodacao_nome
             FROM reservas r
             JOIN clientes c    ON c.id = r.cliente_id
             JOIN acomodacoes a ON a.id = r.acomodacao_id
             {$where}
             ORDER BY r.criado_em DESC
             LIMIT ? OFFSET ?",
            [...$params, $limit, $offset]
        );
    }

    public function countAll(array $filtros = []): int
    {
        [$where, $params] = $this->buildFiltros($filtros);
        return $this->count("SELECT COUNT(*) FROM reservas r {$where}", $params);
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT r.*, c.nome AS cliente_nome, c.email AS cliente_email, c.telefone AS cliente_telefone,
                    a.nome AS acomodacao_nome, a.preco_base
             FROM reservas r
             JOIN clientes c    ON c.id = r.cliente_id
             JOIN acomodacoes a ON a.id = r.acomodacao_id
             WHERE r.id = ?',
            [$id]
        );
    }

    public function findByCodigo(string $codigo): ?array
    {
        return $this->fetchOne('SELECT * FROM reservas WHERE codigo = ?', [$codigo]);
    }

    public function create(array $dados): int
    {
        return $this->insert(
            'INSERT INTO reservas
                (codigo, cliente_id, acomodacao_id, checkin, checkout, adultos, criancas,
                 status, origem, preco_total, desconto_id, desconto_valor, preco_final, observacoes)
             VALUES
                (:codigo, :cliente_id, :acomodacao_id, :checkin, :checkout, :adultos, :criancas,
                 :status, :origem, :preco_total, :desconto_id, :desconto_valor, :preco_final, :observacoes)',
            [
                'codigo'        => $dados['codigo'],
                'cliente_id'    => $dados['cliente_id'],
                'acomodacao_id' => $dados['acomodacao_id'],
                'checkin'       => $dados['checkin'],
                'checkout'      => $dados['checkout'],
                'adultos'       => $dados['adultos'] ?? 1,
                'criancas'      => $dados['criancas'] ?? 0,
                'status'        => $dados['status'] ?? 'pendente',
                'origem'        => $dados['origem'] ?? 'site',
                'preco_total'   => $dados['preco_total'],
                'desconto_id'   => $dados['desconto_id'] ?? null,
                'desconto_valor' => $dados['desconto_valor'] ?? 0,
                'preco_final'   => $dados['preco_final'],
                'observacoes'   => $dados['observacoes'] ?? null,
            ]
        );
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->execute(
            'UPDATE reservas SET status = ? WHERE id = ?',
            [$status, $id]
        ) > 0;
    }

    private function buildFiltros(array $filtros): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($filtros['status'])) {
            $conditions[] = 'r.status = ?';
            $params[]     = $filtros['status'];
        }
        if (!empty($filtros['checkin_de'])) {
            $conditions[] = 'r.checkin >= ?';
            $params[]     = $filtros['checkin_de'];
        }
        if (!empty($filtros['checkin_ate'])) {
            $conditions[] = 'r.checkin <= ?';
            $params[]     = $filtros['checkin_ate'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }
}
