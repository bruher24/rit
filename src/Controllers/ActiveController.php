<?php

namespace App\Controllers;

use App\Services\ActiveService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Validators\Validator;

final class ActiveController
{
    private $app;
    private Validator $validator;
    private ActiveService $activeService;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
        $this->validator = new Validator();
        $this->activeService = new ActiveService();
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        $actives = $this->activeService->getActives();
        $payload = json_encode($actives);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        if (!$this->validator->validate($data)) {
            $errors = $this->validator->getErrors();
            $payload = json_encode(['errors' => $errors]);
        }
        if (empty($payload)) {
            if ($this->activeService->storeActive($data)) {
                $payload = json_encode(['status' => 'success']);
            }
            if (empty($payload)) {
                $errors = [
                    'update' => 'Ошибка при создании актива!',
                ];
                $payload = json_encode(['errors' => $errors]);
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        if (!$this->validator->validate($data)) {
            $errors = $this->validator->getErrors();
            $payload = json_encode(['errors' => $errors]);
        }
        if (empty($payload)) {
            if ($this->activeService->updateActive($data)) {
                $payload = json_encode(['status' => 'success']);
            }
            if (empty($payload)) {
                $errors = [
                    'update' => 'Ошибка при обновлении актива!',
                ];
                $payload = json_encode(['errors' => $errors]);
            }
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $request->getAttribute('id');

        if ($this->activeService->deleteActive($id)) {
            $payload = json_encode(['status' => 'success']);
        }
        if (empty($payload)) {
            $errors = [
                'delete' => 'Ошибка при удалении актива!',
            ];
            $payload = json_encode(['errors' => $errors]);
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
