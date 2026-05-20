<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class HttpValidationException extends RuntimeException
{
    public function __construct(private readonly array $errors, string $message = 'Dados inválidos')
    {
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
