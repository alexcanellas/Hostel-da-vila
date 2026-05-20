<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\HttpValidationException;
use Psr\Http\Message\ResponseInterface;

abstract class BaseController
{
    protected function json(ResponseInterface $response, mixed $data, int $status = 200): ResponseInterface
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    protected function created(ResponseInterface $response, mixed $data): ResponseInterface
    {
        return $this->json($response, $data, 201);
    }

    protected function noContent(ResponseInterface $response): ResponseInterface
    {
        return $response->withStatus(204);
    }

    protected function paginate(array $items, int $total, int $pagina, int $por_pagina): array
    {
        return [
            'data'       => $items,
            'paginacao'  => [
                'total'      => $total,
                'pagina'     => $pagina,
                'por_pagina' => $por_pagina,
                'total_pags' => (int) ceil($total / $por_pagina),
            ],
        ];
    }

    protected function getBody(array $request, array $required = []): array
    {
        $errors = [];
        foreach ($required as $field) {
            if (!isset($request[$field]) || $request[$field] === '') {
                $errors[$field] = "Campo '{$field}' é obrigatório";
            }
        }
        if ($errors) {
            throw new HttpValidationException($errors);
        }
        return $request;
    }

    protected function getPaginationParams(array $queryParams): array
    {
        $pagina    = max(1, (int) ($queryParams['pagina'] ?? 1));
        $por_pagina = min(100, max(1, (int) ($queryParams['por_pagina'] ?? 20)));
        $offset    = ($pagina - 1) * $por_pagina;
        return [$pagina, $por_pagina, $offset];
    }
}
