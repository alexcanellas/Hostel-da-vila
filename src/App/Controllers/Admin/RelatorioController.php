<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ReservaRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RelatorioController extends BaseController
{
    public function __construct(private readonly ReservaRepository $repo) {}

    public function ocupacao(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $de     = $params['de']  ?? date('Y-m-01');
        $ate    = $params['ate'] ?? date('Y-m-t');

        $reservas = $this->repo->findAll(
            ['checkin_de' => $de, 'checkin_ate' => $ate],
            1000,
            0
        );

        return $this->json($response, [
            'periodo'  => ['de' => $de, 'ate' => $ate],
            'reservas' => $reservas,
            'total'    => count($reservas),
        ]);
    }

    public function financeiro(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $de     = $params['de']  ?? date('Y-m-01');
        $ate    = $params['ate'] ?? date('Y-m-t');

        $reservas = $this->repo->findAll(
            ['checkin_de' => $de, 'checkin_ate' => $ate, 'status' => 'confirmada'],
            1000,
            0
        );

        $totalFaturado = array_sum(array_column($reservas, 'preco_final'));

        return $this->json($response, [
            'periodo'        => ['de' => $de, 'ate' => $ate],
            'total_faturado' => round($totalFaturado, 2),
            'total_reservas' => count($reservas),
            'reservas'       => $reservas,
        ]);
    }
}
