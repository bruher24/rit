<?php

namespace App\Controllers;

use App\Models\Active\MoneyActive\BankMoneyActive;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Validators\Validator;

class ActiveController
{
    private $app;
    private array $actives;
    private string $storageFile = __DIR__ . '/../storage/actives.json';
    private Validator $validator;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
        $this->actives = $this->loadActives();
        $this->validator = new Validator();
    }

    public function index(Request $request, Response $response, array $args): Response
    {
        $payload = json_encode($this->actives);
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
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
        $data['id'] = $this->getNextId();
        $this->actives[] = $data;
        $this->saveActives();
        $payload = json_encode(['status' => 'success']);
        $response->getBody()->write($payload);
        return $response;
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        if (!$this->validator->validate($data)) {
            $errors = $this->validator->getErrors();
            $payload = json_encode(['errors' => $errors]);
            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
        $id = (int)$data['id'];
        foreach ($this->actives as $key => $active) {
            if ($active['id'] === $id) {
                $this->actives[$key] = $data;
            }
        }
        $this->saveActives();
        $payload = json_encode(['status' => 'success']);
        $response->getBody()->write($payload);
        return $response;
    }

    public function delete(Request $request, Response $response, array $args): Response
    {

    }

    public function getActivesTypes(Request $request, Response $response, array $args): Response
    {

    }

    private function loadActives(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $json = file_get_contents($this->storageFile);
        return json_decode($json, true) ?? [];
    }

    private function saveActives(): void
    {
        file_put_contents($this->storageFile, json_encode($this->actives, JSON_PRETTY_PRINT));
    }

    private function getNextId(): int
    {
        $lastId = $this->actives[array_key_last($this->actives)]['id'];
        return $lastId + 1;

    }
}
