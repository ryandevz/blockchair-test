<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Utils\Logger;
use App\Database\DatabaseConnection;
use App\Blockchain\Zcash;
use App\Blockchain\ZcashForkMonitor;

class ZcashSynchronizer {
    private $env;
    private $logger;
    private $database;
    private $zcash;

    public function __construct(Env $env, Logger $logger, DatabaseConnection $database) {
        $this->env = $env;
        $this->logger = $logger;
        $this->database = $database;
        $this->zcash = new Zcash(
            $env->get('ZCASH_URL'),
            $env->get('ZCASH_USERNAME'),
            $env->get('ZCASH_PASSWORD')
        );
    }

    public function synchronizeBlock($height) {
        try {
            $block = $this->zcash->getBlock($height);

            /* Insert block */
            $this->database->query(
                "INSERT INTO zcash_blocks (height, hash, size, time, transactions, version, block, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON CONFLICT DO NOTHING",
                [
                    $block['height'], 
                    $block['hash'],
                    $block['size'],
                    $block['time'],
                    count($block['tx'] ?? []),
                    $block['version'],
                    json_encode($block),
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s')
                ]
            );

            /* RPC error until fully indexed - This wallet operation is disabled while reindexing. */
            $transaction = $this->zcash->getTransaction($block['tx']);

            foreach ($transaction['txs'] ?? [] as $key => $value) {
                /* Insert transaction */
                $this->database->query(
                    "INSERT INTO zcash_transactions (height_id, txid, transaction, created_at, updated_at) VALUES (?, ?, ?, ?, ?) ON CONFLICT DO NOTHING",
                    [
                        $value['blockindex'], 
                        $value['txid'],
                        json_encode($value),
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s')
                    ]
                );
            }

            $this->logger->info("Successfully synchronized block {$height}");
            return true;
        } catch (Exception $e) {
            $this->logger->error("Failed to sync block {$height}: " . $e->getMessage());
            return false;
        }
    }

    public function synchronizeBlockRange($start, $end) {
        $results = [
            'success' => 0,
            'failed' => 0
        ];
        
        for ($height = $start; $height <= $end; $height++) {
            echo "\rSynchronized $height blocks out of $end";
            flush();

            if ($this->synchronizeBlock($height)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }
        echo "\n";

        return $results;
    }
}

$arguments = array_slice($argv, 1);

try {
    /* Load environment variables */
    $env = new Env(__DIR__ . '/../.env');
    $env->load();

    /* Initialize Logger */
    $logger = new Logger($env->get('LOG_PATH'), $env->get('LOG_LEVEL'));
    $logger->info('Application zcash started');

    $configuration = [
        'host' => $env->get('DB_HOST'),
        'port' => $env->get('DB_PORT'),
        'dbname' => $env->get('DB_NAME'),
        'user' => $env->get('DB_USER'),
        'password' => $env->get('DB_PASSWORD')
    ];

    /* Get database instance */
    $database = DatabaseConnection::getInstance($configuration);

    /* Initialize the synchronizer */
    $sync = new ZcashSynchronizer($env, $logger, $database);

    if (count($arguments) == 1) {
        /* Synchronization single block */
        $sync->synchronizeBlock($arguments[0]);
    }
    
    if (count($arguments) == 2) {
        /* Synchronization range of block */
        $syncResult = $sync->synchronizeBlockRange($arguments[0], $arguments[1]);
        $logger->info("success: " . $syncResult['success'] . ' and ' . 'failed: ' . $syncResult['failed']);
    }
    
    /* Fork notification */
    $monitor = new ZcashForkMonitor();
    $monitor->checkForUpdates();

} catch (\RuntimeException $e) {
    $logger->error($e->getMessage());
}
