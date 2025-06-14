<?php

use App\Controllers\ActiveController;
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

AppFactory::setContainer($container);
$app = AppFactory::create();

$container = $app->getContainer();
$container->set('view', function () {
    return Twig::create('../templates', ['cache' => false]);
});

$container->set('ActiveController', function () use ($container) {
    return new ActiveController($container);
});

$app->get('/', function ($request, $response, array $args) {
    $file = 'views/index.html';
    if (!file_exists($file)) {
        return $response->withStatus(404);
    }
    $response->getBody()->write(file_get_contents($file));
    return $response;
})->setName('index');
$app->get('/actives', ActiveController::class . ':index')->setName('actives.index');
$app->post('/actives/create', ActiveController::class . ":create")->setName('actives.create');
$app->post('/actives/{id:\d+}/update', ActiveController::class . ":update")->setName('actives.update');
$app->delete('/actives/{id:\d+}/delete', ActiveController::class . ":delete")->setName('actives.delete');

$app->run();
