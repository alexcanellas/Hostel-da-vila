<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\ReservaService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReservaController extends BaseController
{
    public function __construct(private readonly ReservaService $service) {}

    public function listar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params               = $request->getQueryParams();
        [$pagina, $por, $offset] = $this->getPaginationParams($params);

        $filtros = array_filter([
            'status'      => $params['status']      ?? null,
            'checkin_de'  => $params['checkin_de']  ?? null,
            'checkin_ate' => $params['checkin_ate'] ?? null,
        ]);

        [$items, $total] = $this->service->listar($filtros, $pagina, $por);

        return $this->json($response, $this->paginate($items, $total, $pagina, $por));
    }

    public function buscar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->json($response, $this->service->buscar((int) $args['id']));
    }

    public function criar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->getBody($body, ['acomodacao_id', 'checkin', 'checkout', 'cliente']);
        $this->getBody((array) ($body['cliente'] ?? []), ['nome', 'email']);

        $reserva = $this->service->criar($body);
        return $this->created($response, $reserva);
    }

    public function atualizar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body  = (array) $request->getParsedBody();
        // Apenas status é atualizável por esta rota por ora
        if (!empty($body['status'])) {
            $this->service->buscar((int) $args['id']); // valida existência
        }
        return $this->json($response, $this->service->buscar((int) $args['id']));
    }

    public function cancelar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->service->cancelar((int) $args['id']);
        return $this->noContent($response);
    }
}
