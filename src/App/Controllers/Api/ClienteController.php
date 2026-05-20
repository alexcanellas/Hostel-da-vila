<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Repositories\ClienteRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ClienteController extends BaseController
{
    public function __construct(private readonly ClienteRepository $repo) {}

    public function listar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params               = $request->getQueryParams();
        [$pagina, $por, $offset] = $this->getPaginationParams($params);

        $items = $this->repo->findAll($por, $offset);
        $total = $this->repo->countAll();

        return $this->json($response, $this->paginate($items, $total, $pagina, $por));
    }

    public function buscar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $cliente = $this->repo->findById((int) $args['id']);
        if (!$cliente) {
            return $this->json($response, ['error' => 'Cliente não encontrado'], 404);
        }
        return $this->json($response, $cliente);
    }
}
