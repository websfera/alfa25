<?php

use App\Config\Config;
use App\Controller\HomeController;
use App\Controller\LogController;
use App\DI\Container;
use App\Router\Router;
use Tracy\Debugger;

session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
}

require 'vendor/autoload.php';
Debugger::enable(false);

Debugger::barDump($_SESSION);

$config = new Config();
$config->load(__DIR__ . '/config/config.yaml');
$config->load(__DIR__ . '/config/services.yaml');

$di = new Container($config);

$router = new Router();
// HomeController
$router->addRoute("/", HomeController::class, "index");
$router->addRoute("/o-aplikaci", HomeController::class, "about");
$router->addRoute("/o-vyvojari", HomeController::class, "developer");
$router->addRoute("/kontakt", HomeController::class, "contact");

// LogController
$router->addRoute("/prihlaseni", LogController::class,'in');
$router->addRoute("/odhlaseni", LogController::class,'out');

// UserController
$router->addRoute('/uzivatel/{id}', 'UserController', 'detail');

$di->addService('router', $router);

$router->dispatch($_SERVER['REQUEST_URI'], $di);

//Debugger::dump($_SERVER);
//Debugger::dump($matchedRoute);

//$controller = new HomeController();
//
//$controller->draw();
