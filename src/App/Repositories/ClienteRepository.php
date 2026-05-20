<?php

declare(strict_types=1);

namespace App\Repositories;

class ClienteRepository extends BaseRepository
{
    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM clientes WHERE email = ?', [$email]);
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM clientes WHERE id = ?', [$id]);
    }

    public function findAll(int $limit = 20, int $offset = 0): array
    {
        return $this->fetchAll(
            'SELECT * FROM clientes ORDER BY nome LIMIT ? OFFSET ?',
            [$limit, $offset]
        );
    }

    public function countAll(): int
    {
        return $this->count('SELECT COUNT(*) FROM clientes');
    }

    public function upsert(array $dados): int
    {
        $existente = $this->findByEmail($dados['email']);
        if ($existente) {
            $this->execute(
                'UPDATE clientes SET nome = :nome, telefone = :telefone, documento = :documento, pais = :pais WHERE email = :email',
                [
                    'nome'      => $dados['nome'],
                    'telefone'  => $dados['telefone'] ?? null,
                    'documento' => $dados['documento'] ?? null,
                    'pais'      => $dados['pais'] ?? 'BR',
                    'email'     => $dados['email'],
                ]
            );
            return $existente['id'];
        }

        return $this->insert(
            'INSERT INTO clientes (nome, email, telefone, documento, pais) VALUES (:nome, :email, :telefone, :documento, :pais)',
            [
                'nome'      => $dados['nome'],
                'email'     => $dados['email'],
                'telefone'  => $dados['telefone'] ?? null,
                'documento' => $dados['documento'] ?? null,
                'pais'      => $dados['pais'] ?? 'BR',
            ]
        );
    }
}
