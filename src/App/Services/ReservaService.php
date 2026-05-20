<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HttpValidationException;
use App\Exceptions\NotFoundException;
use App\Repositories\AcomodacaoRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\DescontoRepository;
use App\Repositories\ReservaRepository;
use Psr\Log\LoggerInterface;

class ReservaService
{
    public function __construct(
        private readonly ReservaRepository    $reservaRepo,
        private readonly AcomodacaoRepository $acomodacaoRepo,
        private readonly ClienteRepository    $clienteRepo,
        private readonly DescontoRepository   $descontoRepo,
        private readonly LoggerInterface      $logger
    ) {}

    public function criar(array $dados): array
    {
        // Valida acomodação
        $acomodacao = $this->acomodacaoRepo->findById((int) $dados['acomodacao_id']);
        if (!$acomodacao) {
            throw new HttpValidationException(['acomodacao_id' => 'Acomodação não encontrada']);
        }

        // Valida datas
        $checkin  = $dados['checkin'];
        $checkout = $dados['checkout'];
        if ($checkin >= $checkout) {
            throw new HttpValidationException(['checkout' => 'Checkout deve ser posterior ao checkin']);
        }
        if ($checkin < date('Y-m-d')) {
            throw new HttpValidationException(['checkin' => 'Checkin não pode ser no passado']);
        }

        // Verifica disponibilidade
        if (!$this->acomodacaoRepo->isDisponivel((int) $dados['acomodacao_id'], $checkin, $checkout)) {
            throw new HttpValidationException(['acomodacao_id' => 'Acomodação indisponível para o período selecionado']);
        }

        // Calcula preço
        $noites     = (int) ((new \DateTime($checkout))->diff(new \DateTime($checkin))->days);
        $precoTotal = round((float) $acomodacao['preco_base'] * $noites, 2);

        // Aplica desconto
        $descontoId    = null;
        $descontoValor = 0.0;
        if (!empty($dados['cupom'])) {
            $desconto = $this->descontoRepo->findByCodigo($dados['cupom']);
            if (!$desconto) {
                throw new HttpValidationException(['cupom' => 'Cupom inválido ou expirado']);
            }
            $descontoId    = $desconto['id'];
            $descontoValor = $desconto['tipo'] === 'percentual'
                ? round($precoTotal * ($desconto['valor'] / 100), 2)
                : min((float) $desconto['valor'], $precoTotal);
        }

        $precoFinal = max(0, $precoTotal - $descontoValor);

        // Upsert cliente
        $clienteId = $this->clienteRepo->upsert($dados['cliente']);

        // Cria reserva
        $id = $this->reservaRepo->create([
            'codigo'        => $this->gerarCodigo(),
            'cliente_id'    => $clienteId,
            'acomodacao_id' => (int) $dados['acomodacao_id'],
            'checkin'       => $checkin,
            'checkout'      => $checkout,
            'adultos'       => $dados['adultos'] ?? 1,
            'criancas'      => $dados['criancas'] ?? 0,
            'preco_total'   => $precoTotal,
            'desconto_id'   => $descontoId,
            'desconto_valor' => $descontoValor,
            'preco_final'   => $precoFinal,
            'observacoes'   => $dados['observacoes'] ?? null,
        ]);

        if ($descontoId) {
            $this->descontoRepo->incrementarUso($descontoId);
        }

        $reserva = $this->reservaRepo->findById($id);
        $this->logger->info('Reserva criada', ['reserva_id' => $id, 'codigo' => $reserva['codigo']]);

        return $reserva;
    }

    public function listar(array $filtros, int $pagina, int $porPagina): array
    {
        $offset  = ($pagina - 1) * $porPagina;
        $items   = $this->reservaRepo->findAll($filtros, $porPagina, $offset);
        $total   = $this->reservaRepo->countAll($filtros);
        return [$items, $total];
    }

    public function buscar(int $id): array
    {
        $reserva = $this->reservaRepo->findById($id);
        if (!$reserva) {
            throw new NotFoundException("Reserva #{$id} não encontrada");
        }
        return $reserva;
    }

    public function cancelar(int $id): void
    {
        $reserva = $this->buscar($id);
        if (in_array($reserva['status'], ['cancelada', 'concluida'], true)) {
            throw new HttpValidationException(['status' => 'Reserva não pode ser cancelada']);
        }
        $this->reservaRepo->updateStatus($id, 'cancelada');
    }

    private function gerarCodigo(): string
    {
        return 'HDV-' . strtoupper(substr(uniqid('', true), -8));
    }
}
