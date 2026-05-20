<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\AcomodacaoService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AcomodacaoController extends BaseController
{
    public function __construct(private readonly AcomodacaoService $service) {}

    public function listar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->json($response, $this->service->listar());
    }

    public function buscar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->json($response, $this->service->buscar((int) $args['id']));
    }

    public function disponibilidade(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params   = $request->getQueryParams();
        $checkin  = $params['checkin']  ?? '';
        $checkout = $params['checkout'] ?? '';

        if (!$checkin || !$checkout) {
            return $this->json($response, ['error' => 'Parâmetros checkin e checkout são obrigatórios'], 400);
        }

        return $this->json($response, $this->service->verificarDisponibilidade((int) $args['id'], $checkin, $checkout));
    }
}
