<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'src/config.php';

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();


$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->query("SET NAMES UTF8");
    return $pdo;
};

require 'src/view_auth.php';


$app->post('/user', '\AuthAction:signup');
$app->post('/auth', '\AuthAction:auth');
$app->get('/{params:.*}',function ($request, $response, $args) {
    $response->getBody()->write("Hello!");
});

$app->run();
