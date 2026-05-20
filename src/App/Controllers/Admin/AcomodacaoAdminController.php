<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AcomodacaoService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AcomodacaoAdminController extends BaseController
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

    public function criar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->getBody($body, ['nome', 'capacidade', 'preco_base']);

        return $this->created($response, $this->service->criar($body));
    }

    public function atualizar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->getBody($body, ['nome', 'capacidade', 'preco_base']);

        return $this->json($response, $this->service->atualizar((int) $args['id'], $body));
    }

    public function remover(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->service->remover((int) $args['id']);
        return $this->noContent($response);
    }
}
