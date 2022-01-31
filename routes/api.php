<?php

use App\Controllers\BalanceController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use App\Lib\Request;
use App\Lib\Response;
use App\Lib\Router;

include __DIR__ . '/../store/dataset.php';

$router = new Router();

$router->get('/balance', BalanceController::class.'::getById');
$router->post('/event', EventController::class.'::managerAccount');


$router->get('/home', HomeController::class. '::index');

$router->post('/reset', function(Request $req, Response $res) {
    $path = __DIR__ . '/../store/db.json';
    file_put_contents($path, json_encode([]));
    $res->status(200)->noContent();
});

$router->addNotFoundHandler(function(Request $req, Response $res) {
    $res->status(404)->toJSON('Not Found');
});

$router->run();