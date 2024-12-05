<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Utils\Logger;
use App\Database\DatabaseConnection;

/* Load environment variables */
$env = new Env(__DIR__ . '/../.env');

try {
    $env->load();
} catch (RuntimeException $e) {
    echo "Error loading .env file: " . $e->getMessage();
}

/* Initialize Logger */
$logger = new Logger($env->get('LOG_PATH'), $env->get('LOG_LEVEL'));
$logger->info('Application started');

die(var_dump('Hello World!'));