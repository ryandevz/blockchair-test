<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Utils\Logger;
use App\Database\DatabaseConnection;
use App\Blockchain\Monero;
use App\Blockchain\Zcash;

/* Load environment variables */
$env = new Env(__DIR__ . '/../.env');

try {
    $env->load();
} catch (RuntimeException $e) {
    $logger->error("Error loading .env file: " . $e->getMessage());
}

/* Initialize Logger */
$logger = new Logger($env->get('LOG_PATH'), $env->get('LOG_LEVEL'));
$logger->info('Application zcash started');

try {
    /* Zcash client */
    $zcash = new Zcash(
        $env->get('ZCASH_URL'),
        $env->get('ZCASH_USERNAME'),
        $env->get('ZCASH_PASSWORD')
    );
    
    $info = $zcash->getBlockchainInfo();
} catch (\RuntimeException $e) {
    $logger->error($e->getMessage());
}