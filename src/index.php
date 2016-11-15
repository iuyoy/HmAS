<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

date_default_timezone_set('Europe/Berlin');

require 'vendor/autoload.php';
require 'src/config.php';

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->query("SET NAMES UTF8");
    return $pdo;
};

require 'src/view_base.php';
require 'src/view_user.php';
require 'src/view_sensor.php';
require 'src/view_happiness.php';

$app->post('/user/', '\UserAction:signup');
$app->post('/user', '\UserAction:signup');
$app->post('/auth/', '\UserAction:auth');
$app->post('/auth', '\UserAction:auth');
$app->post('/bind/', '\UserAction:bind');
$app->post('/bind', '\UserAction:bind');
$app->post('/userinfo/', '\UserAction:update');
$app->post('/userinfo', '\UserAction:update');
// $app->post('/update', '\UserAction:update');
$app->post('/sensor/', '\SensorAction:collectData');
$app->post('/sensor', '\SensorAction:collectData');
$app->post('/happiness/', '\HappinessAction:collectData');
$app->post('/happiness', '\HappinessAction:collectData');
$app->get('/{params:.*}',function ($request, $response, $args) {

    // var_dump(checkTime($_GET['time']));
    $response->getBody()->write("Hello!");
});

$app->run();
