<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Repositories\PagamentoRepository;
use App\Services\PagamentoService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PagamentoController extends BaseController
{
    public function __construct(
        private readonly PagamentoService    $service,
        private readonly PagamentoRepository $repo
    ) {}

    public function gerarLink(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $this->getBody($body, ['reserva_id']);

        $pagamento = $this->service->gerarLink((int) $body['reserva_id']);
        return $this->created($response, $pagamento);
    }

    public function buscar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $pagamento = $this->repo->findById((int) $args['id']);
        if (!$pagamento) {
            return $this->json($response, ['error' => 'Pagamento não encontrado'], 404);
        }
        return $this->json($response, $pagamento);
    }
}
