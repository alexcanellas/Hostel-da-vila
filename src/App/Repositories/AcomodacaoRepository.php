<?php

declare(strict_types=1);

namespace App\Repositories;

class AcomodacaoRepository extends BaseRepository
{
    public function findAll(bool $soAtivas = true): array
    {
        $where = $soAtivas ? 'WHERE a.ativo = 1' : '';
        return $this->fetchAll(
            "SELECT a.*, t.nome AS tipo_nome
             FROM acomodacoes a
             LEFT JOIN tipos_acomodacao t ON t.id = a.tipo_id
             {$where}
             ORDER BY a.nome"
        );
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT a.*, t.nome AS tipo_nome
             FROM acomodacoes a
             LEFT JOIN tipos_acomodacao t ON t.id = a.tipo_id
             WHERE a.id = ?',
            [$id]
        );
    }

    public function create(array $dados): int
    {
        return $this->insert(
            'INSERT INTO acomodacoes (tipo_id, nome, descricao, capacidade, preco_base, comodidades)
             VALUES (:tipo_id, :nome, :descricao, :capacidade, :preco_base, :comodidades)',
            [
                'tipo_id'    => $dados['tipo_id']    ?? null,
                'nome'       => $dados['nome'],
                'descricao'  => $dados['descricao']  ?? null,
                'capacidade' => $dados['capacidade'],
                'preco_base' => $dados['preco_base'],
                'comodidades' => isset($dados['comodidades']) ? json_encode($dados['comodidades']) : null,
            ]
        );
    }

    public function update(int $id, array $dados): bool
    {
        $linhas = $this->execute(
            'UPDATE acomodacoes
             SET tipo_id = :tipo_id, nome = :nome, descricao = :descricao,
                 capacidade = :capacidade, preco_base = :preco_base,
                 comodidades = :comodidades, ativo = :ativo
             WHERE id = :id',
            [
                'id'         => $id,
                'tipo_id'    => $dados['tipo_id']    ?? null,
                'nome'       => $dados['nome'],
                'descricao'  => $dados['descricao']  ?? null,
                'capacidade' => $dados['capacidade'],
                'preco_base' => $dados['preco_base'],
                'comodidades' => isset($dados['comodidades']) ? json_encode($dados['comodidades']) : null,
                'ativo'      => $dados['ativo'] ?? 1,
            ]
        );
        return $linhas > 0;
    }

    public function delete(int $id): bool
    {
        return $this->execute('UPDATE acomodacoes SET ativo = 0 WHERE id = ?', [$id]) > 0;
    }

    public function isDisponivel(int $id, string $checkin, string $checkout): bool
    {
        $count = $this->count(
            "SELECT COUNT(*) FROM reservas
             WHERE acomodacao_id = ?
               AND status NOT IN ('cancelada','no_show')
               AND checkin < ?
               AND checkout > ?",
            [$id, $checkout, $checkin]
        );
        return $count === 0;
    }
}
