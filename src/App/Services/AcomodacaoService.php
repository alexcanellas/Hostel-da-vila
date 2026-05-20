<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Repositories\AcomodacaoRepository;

class AcomodacaoService
{
    public function __construct(private readonly AcomodacaoRepository $repo) {}

    public function listar(): array
    {
        return $this->repo->findAll();
    }

    public function buscar(int $id): array
    {
        $acomodacao = $this->repo->findById($id);
        if (!$acomodacao) {
            throw new NotFoundException("Acomodação #{$id} não encontrada");
        }

        if ($acomodacao['fotos']) {
            $acomodacao['fotos'] = json_decode($acomodacao['fotos'], true);
        }
        if ($acomodacao['comodidades']) {
            $acomodacao['comodidades'] = json_decode($acomodacao['comodidades'], true);
        }

        return $acomodacao;
    }

    public function verificarDisponibilidade(int $id, string $checkin, string $checkout): array
    {
        $acomodacao = $this->buscar($id);
        $disponivel = $this->repo->isDisponivel($id, $checkin, $checkout);

        return [
            'acomodacao_id' => $id,
            'nome'          => $acomodacao['nome'],
            'checkin'       => $checkin,
            'checkout'      => $checkout,
            'disponivel'    => $disponivel,
            'preco_base'    => $acomodacao['preco_base'],
        ];
    }

    public function criar(array $dados): array
    {
        $id = $this->repo->create($dados);
        return $this->buscar($id);
    }

    public function atualizar(int $id, array $dados): array
    {
        $this->buscar($id);
        $this->repo->update($id, $dados);
        return $this->buscar($id);
    }

    public function remover(int $id): void
    {
        $this->buscar($id);
        $this->repo->delete($id);
    }
}
