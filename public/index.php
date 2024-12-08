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

$configuration = [
    'host' => $env->get('DB_HOST'),
    'port' => $env->get('DB_PORT'),
    'dbname' => $env->get('DB_NAME'),
    'user' => $env->get('DB_USER'),
    'password' => $env->get('DB_PASSWORD')
];

/* Get database instance */
$database = DatabaseConnection::getInstance($configuration);

$request = new Request();
$response = new Response();
$router = new Router();
$dataController = new DataController($env, $logger, $database);

/* Define routes */
$router->addRoute('GET', '/api/monero', [$dataController, 'getMonero']);
$router->addRoute('GET', '/api/zcash', [$dataController, 'getZcash']);

$router->addRoute('GET', '/api/monero/block/{id}', [$dataController, 'moneroBlock']);
$router->addRoute('GET', '/api/zcash/block/{id}', [$dataController, 'zcashBlock']);

$router->addRoute('GET', '/api/monero/transaction/{id}', [$dataController, 'moneroTransaction']);
$router->addRoute('GET', '/api/zcash/transaction/{id}', [$dataController, 'zcashTransaction']);

/* Handle the request */
$router->dispatch($request, $response);