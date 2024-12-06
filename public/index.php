<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Utils\Logger;
use App\Database\DatabaseConnection;
use App\Http\Request;
use App\Http\Response;
use App\Router\Router;
use App\Controllers\DataController;

/* Load environment variables */
$env = new Env(__DIR__ . '/../.env');

try {
    $env->load();
} catch (RuntimeException $e) {
    $logger->error("Error loading .env file: " . $e->getMessage());
}

/* Initialize Logger */
$logger = new Logger($env->get('LOG_PATH'), $env->get('LOG_LEVEL'));
$logger->info('Application api started');

$request = new Request();
$response = new Response();
$router = new Router();
$dataController = new DataController();

/* Define routes */
$router->addRoute('GET', '/api/monero', [$dataController, 'getMonero']);
$router->addRoute('POST', '/api/zcash', [$dataController, 'getZcash']);

/* Handle the request */
$router->dispatch($request, $response);