<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

abstract class RestController
{
    protected ContainerInterface $container;

    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    protected array $args;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        try {
            return $this->action();
        } catch (\Exception $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    abstract protected function action(): ResponseInterface;

    protected function respondWithData($data = null, int $statusCode = 200, $statusCookie = false): ResponseInterface
    {
        $this->response->getBody()->write(json_encode($data));

        $newResponse = $this->response->withHeader('Content-Type', 'application/json');

        if ($statusCookie) {
            $newResponse = $this->response->withHeader('Set-Cookie', $statusCookie);
        }

        return $newResponse->withStatus($statusCode);
    }

    // TODO: Реализовать метод для подгрузки xlsx
    protected function respondWithFileXlsx($filePath, $outputName = null, int $statusCode = 200): ResponseInterface
    {

    }

    protected function respondWithError(int $errorCode, mixed $errorMessage = null): ResponseInterface
    {
        return match ($errorCode) {
            253 => $this->respondWithData(['code' => 253, 'message' => $errorMessage], 400),
            221 => $this->respondWithData(['code' => 221, 'message' => 'Нет прав на выполнение действия.'], 400),
            215 => $this->respondWithData(['code' => 215, 'message' => 'Истек срок действия токена'], 401),
            219 => $this->respondWithData(['code' => 219, 'message' => 'refresh token not found'], 400),
            217 => $this->respondWithData(['code' => 217, 'message' => 'refresh token expired'], 401),
            0, 404 => $this->respondWithData(['code' => 404, 'message' => 'Страница не найдена'], 404),
            default => $this->respondWithData($errorMessage, 400),
        };
    }
}