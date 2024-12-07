<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Utils\Logger;
use App\Database\DatabaseConnection;
use App\Blockchain\Monero;
use App\Blockchain\Zcash;

class MoneroSynchronizer {
    private $env;
    private $logger;
    private $database;
    private $monero;

    public function __construct(Env $env, Logger $logger, DatabaseConnection $database) {
        $this->env = $env;
        $this->logger = $logger;
        $this->database = $database;
        $this->monero = new Monero($env->get('MONERO_URL'));
    }

    public function synchronizeBlock($height) {
        try {
            $block = $this->monero->getBlock($height);

            /* Insert block */
            $this->database->query(
                "INSERT INTO monero_blocks (height, hash, miner_tx_hash, difficulty, size, timestamp, transactions, major_version, minor_version, block, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON CONFLICT DO NOTHING",
                [
                    $block['block_header']['height'], 
                    $block['block_header']['hash'],
                    $block['block_header']['miner_tx_hash'],
                    $block['block_header']['difficulty'],
                    $block['block_header']['block_size'],
                    $block['block_header']['timestamp'],
                    count($block['tx_hashes'] ?? []),
                    $block['block_header']['major_version'],
                    $block['block_header']['minor_version'],
                    json_encode($block),
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s')
                ]
            );

            $transaction = $this->monero->getTransaction($block['tx_hashes']);

            foreach ($transaction['txs'] ?? [] as $key => $value) {
                /* Insert transaction */
                $this->database->query(
                    "INSERT INTO monero_transactions (height_id, tx_hash, transaction, created_at, updated_at) VALUES (?, ?, ?, ?, ?) ON CONFLICT DO NOTHING",
                    [
                        $value['block_height'], 
                        $value['tx_hash'],
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
            if ($this->synchronizeBlock($height)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }
}

try {
    /* Load environment variables */
    $env = new Env(__DIR__ . '/../.env');
    $env->load();

    /* Initialize Logger */
    $logger = new Logger($env->get('LOG_PATH'), $env->get('LOG_LEVEL'));
    $logger->info('Application monero started');

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
    $sync = new MoneroSynchronizer($env, $logger, $database);

    /* Synchronization single block */
    // $sync->synchronizeBlock(1873006);

    /* Synchronization range of block */
    $syncResult = $sync->synchronizeBlockRange(1873000, 1873009);
    $logger->info("success: " . $syncResult['success'] . ' and ' . 'failed: ' . $syncResult['failed']);

    /* Fork notification */
    
    /* Get last known block */

} catch (\RuntimeException $e) {
    $logger->error($e->getMessage());
}