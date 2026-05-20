<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\AcomodacaoRepository;
use App\Repositories\ReservaRepository;
use App\Repositories\PagamentoRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController extends BaseController
{
    public function __construct(
        private readonly ReservaRepository    $reservaRepo,
        private readonly AcomodacaoRepository $acomodacaoRepo,
        private readonly PagamentoRepository  $pagamentoRepo
    ) {}

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $hoje    = date('Y-m-d');
        $mes     = date('Y-m-01');
        $mesFim  = date('Y-m-t');

        $data = [
            'reservas' => [
                'total_mes'    => $this->reservaRepo->countAll(['checkin_de' => $mes, 'checkin_ate' => $mesFim]),
                'pendentes'    => $this->reservaRepo->countAll(['status' => 'pendente']),
                'confirmadas'  => $this->reservaRepo->countAll(['status' => 'confirmada']),
                'checkins_hoje' => $this->reservaRepo->countAll(['checkin_de' => $hoje, 'checkin_ate' => $hoje]),
            ],
        ];

        return $this->json($response, $data);
    }
}
