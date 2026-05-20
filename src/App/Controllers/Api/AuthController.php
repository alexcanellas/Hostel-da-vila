<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Repositories\UsuarioRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService       $authService,
        private readonly UsuarioRepository $usuarioRepo
    ) {}

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->getBody($body, ['email', 'senha']);

        $resultado = $this->authService->login($body['email'], $body['senha']);
        if (!$resultado) {
            return $this->json($response, ['error' => 'Credenciais inválidas'], 401);
        }

        return $this->json($response, $resultado);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // JWT é stateless; invalidação real exigiria blacklist no Redis
        return $this->json($response, ['message' => 'Logout realizado']);
    }

    public function me(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $usuarioId = $request->getAttribute('usuario_id');
        $usuario   = $this->usuarioRepo->findById((int) $usuarioId);

        return $this->json($response, $usuario);
    }
}
