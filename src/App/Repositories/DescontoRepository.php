<?php

declare(strict_types=1);

namespace App\Repositories;

class DescontoRepository extends BaseRepository
{
    public function findByCodigo(string $codigo): ?array
    {
        return $this->fetchOne(
            "SELECT * FROM descontos
             WHERE codigo = ?
               AND ativo = 1
               AND (valido_de IS NULL OR valido_de <= CURDATE())
               AND (valido_ate IS NULL OR valido_ate >= CURDATE())
               AND (usos_maximos IS NULL OR usos_atuais < usos_maximos)",
            [$codigo]
        );
    }

    public function findAll(): array
    {
        return $this->fetchAll('SELECT * FROM descontos ORDER BY criado_em DESC');
    }

    public function create(array $dados): int
    {
        return $this->insert(
            'INSERT INTO descontos (codigo, tipo, valor, usos_maximos, valido_de, valido_ate, ativo)
             VALUES (:codigo, :tipo, :valor, :usos_maximos, :valido_de, :valido_ate, 1)',
            [
                'codigo'      => strtoupper($dados['codigo']),
                'tipo'        => $dados['tipo'],
                'valor'       => $dados['valor'],
                'usos_maximos' => $dados['usos_maximos'] ?? null,
                'valido_de'   => $dados['valido_de'] ?? null,
                'valido_ate'  => $dados['valido_ate'] ?? null,
            ]
        );
    }

    public function incrementarUso(int $id): void
    {
        $this->execute('UPDATE descontos SET usos_atuais = usos_atuais + 1 WHERE id = ?', [$id]);
    }

    public function update(int $id, array $dados): bool
    {
        return $this->execute(
            'UPDATE descontos SET tipo = :tipo, valor = :valor, usos_maximos = :usos_maximos,
             valido_de = :valido_de, valido_ate = :valido_ate, ativo = :ativo WHERE id = :id',
            [
                'id'          => $id,
                'tipo'        => $dados['tipo'],
                'valor'       => $dados['valor'],
                'usos_maximos' => $dados['usos_maximos'] ?? null,
                'valido_de'   => $dados['valido_de'] ?? null,
                'valido_ate'  => $dados['valido_ate'] ?? null,
                'ativo'       => $dados['ativo'] ?? 1,
            ]
        ) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->execute('UPDATE descontos SET ativo = 0 WHERE id = ?', [$id]) > 0;
    }
}
