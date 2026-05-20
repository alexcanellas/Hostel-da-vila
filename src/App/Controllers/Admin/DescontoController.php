<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\DescontoRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DescontoController extends BaseController
{
    public function __construct(private readonly DescontoRepository $repo) {}

    public function listar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->json($response, $this->repo->findAll());
    }

    public function criar(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->getBody($body, ['codigo', 'tipo', 'valor']);

        $id = $this->repo->create($body);
        return $this->created($response, ['id' => $id, 'codigo' => strtoupper($body['codigo'])]);
    }

    public function atualizar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->repo->update((int) $args['id'], $body);
        return $this->json($response, ['message' => 'Desconto atualizado']);
    }

    public function remover(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->delete((int) $args['id']);
        return $this->noContent($response);
    }
}
