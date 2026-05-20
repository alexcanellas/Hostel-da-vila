<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\HttpValidationException;
use App\Exceptions\NotFoundException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(App $app, private readonly ?LoggerInterface $logger = null)
    {
        $this->responseFactory = $app->getResponseFactory();
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable              $exception,
        bool                   $displayErrorDetails,
        bool                   $logErrors,
        bool                   $logErrorDetails
    ): ResponseInterface {
        if ($logErrors) {
            $this->logger?->error($exception->getMessage(), [
                'exception' => $exception,
                'url'       => (string) $request->getUri(),
                'method'    => $request->getMethod(),
            ]);
        }

        [$statusCode, $payload] = match (true) {
            $exception instanceof HttpNotFoundException      => [404, ['error' => 'Recurso não encontrado']],
            $exception instanceof HttpMethodNotAllowedException => [405, ['error' => 'Método não permitido']],
            $exception instanceof NotFoundException          => [404, ['error' => $exception->getMessage()]],
            $exception instanceof HttpValidationException    => [422, ['error' => 'Dados inválidos', 'erros' => $exception->getErrors()]],
            default => [500, $this->serverError($exception, $displayErrorDetails)],
        };

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function serverError(Throwable $e, bool $details): array
    {
        $payload = ['error' => 'Erro interno do servidor'];
        if ($details) {
            $payload['message'] = $e->getMessage();
            $payload['trace']   = $e->getTraceAsString();
        }
        return $payload;
    }
}
