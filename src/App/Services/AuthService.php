<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UsuarioRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepo,
        private readonly array             $jwtConfig
    ) {}

    public function login(string $email, string $senha): ?array
    {
        $usuario = $this->usuarioRepo->findByEmail($email);
        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return null;
        }

        $token = $this->generateToken($usuario);

        return [
            'token'   => $token,
            'usuario' => [
                'id'     => $usuario['id'],
                'nome'   => $usuario['nome'],
                'email'  => $usuario['email'],
                'perfil' => $usuario['perfil'],
            ],
        ];
    }

    public function validateToken(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->jwtConfig['secret'], $this->jwtConfig['algorithm']));
        return (array) $decoded;
    }

    private function generateToken(array $usuario): string
    {
        $now = time();
        $payload = [
            'iss'    => 'hosteldavila-api',
            'iat'    => $now,
            'exp'    => $now + $this->jwtConfig['expiration'],
            'sub'    => $usuario['id'],
            'perfil' => $usuario['perfil'],
        ];
        return JWT::encode($payload, $this->jwtConfig['secret'], $this->jwtConfig['algorithm']);
    }
}
