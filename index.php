<?php

use App\Config\Config;
use App\Controller\HomeController;
use App\Controller\LogController;
use App\Controller\MessengerController;
use App\DI\Container;
use App\Router\Router;
use Tracy\Debugger;

session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
}

require 'vendor/autoload.php';
Debugger::enable(false);

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
$router->addRoute("/registrace", LogController::class,'register');
$router->addRoute("/odhlaseni", LogController::class,'out');

// MessengerController
$router->addRoute('/messenger', MessengerController::class, 'index');
$router->addRoute('/messenger/nova/{userId}', MessengerController::class, 'createConversation');
$router->addRoute('/messenger/{conversationId}', MessengerController::class, 'index');

$di->addService('router', $router);

$router->dispatch($_SERVER['REQUEST_URI'], $di);
