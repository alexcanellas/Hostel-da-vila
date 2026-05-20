<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly AuthService $authService) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized('Token não informado');
        }

        $token = substr($authHeader, 7);

        try {
            $payload = $this->authService->validateToken($token);
            $request = $request->withAttribute('usuario_id', $payload['sub'])
                               ->withAttribute('usuario_perfil', $payload['perfil'] ?? 'staff');
            return $handler->handle($request);
        } catch (ExpiredException) {
            return $this->unauthorized('Token expirado');
        } catch (\Throwable) {
            return $this->unauthorized('Token inválido');
        }
    }

    private function unauthorized(string $mensagem): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(json_encode([
            'error'   => 'Não autorizado',
            'message' => $mensagem,
        ]));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
}
