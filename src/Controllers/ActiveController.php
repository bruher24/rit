<?php

namespace App\Controllers;

use App\Models\Active\MoneyActive\BankMoneyActive;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ActiveController
{
    private $app;
    private array $actives;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;

        $this->actives[] = new BankMoneyActive(1,'Main Account', 999.65, 'Сбербанк', '12345');
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
        $data = $request->getAttributes();
//        var_dump($data);
        $file = 'views/index.html';
        if (!file_exists($file)) {
            return $response->withStatus(404);
        }
        $response->getBody()->write(file_get_contents($file));
        return $response;
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $active = $this->actives[$request->getAttribute('id') - 1];
        return $this->app->get('view')->render($response, 'form.twig', ['active' => $active]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {

    }

    public function getActivesTypes(Request $request, Response $response, array $args): Response
    {

    }
}
