<?php

declare(strict_types=1);

namespace App\Repositories;

class UsuarioRepository extends BaseRepository
{
    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM usuarios WHERE email = ? AND ativo = 1',
            [$email]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, nome, email, perfil, ativo, criado_em FROM usuarios WHERE id = ?',
            [$id]
        );
    }
}
