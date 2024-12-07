<?php
namespace App\Blockchain;

use App\Config\Env;
use App\Utils\Logger;
use App\Blockchain\Monero;

class MoneroForkMonitor {
    private $lastKnownVersion;
    private $lastHfVersion;
    private $apiEndpoint = 'https://api.github.com/repos/monero-project/monero/releases/latest';
    private $configFile = '../monero_config.json';
    private $monero;
    private $logger;

    public function __construct() {
        $env = new Env(__DIR__ . '/../../.env');
        $env->load();
        $this->logger = new Logger(__DIR__ . '/../' . $env->get('LOG_PATH'), $env->get('LOG_LEVEL'));
        $this->loadConfig();
        $this->monero = new Monero($env->get('MONERO_URL'));
    }

    private function loadConfig() {
        if (file_exists($this->configFile)) {
            $config = json_decode(file_get_contents($this->configFile), true);

            $this->lastKnownVersion = $config['last_version'] ?? '';
            $this->lastHfVersion = $config['last_hf_version'] ?? '';
        } else {
            $this->saveConfig();
        }
    }

    private function saveConfig() {
        $config = [
            'last_version' => $this->lastKnownVersion,
            'last_hf_version' => $this->lastHfVersion,
            'last_check' => date('Y-m-d H:i:s')
        ];

        file_put_contents(
            $this->configFile, 
            json_encode($config, JSON_PRETTY_PRINT)
        );
    }

    public function checkForUpdates() {
        try {
            $this->checkGithubReleases();
            $this->checkNetwork();
            $this->saveConfig();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    private function checkGithubReleases() {
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ];
        
        $context = stream_context_create($options);
        $response = file_get_contents($this->apiEndpoint, false, $context);
        
        if ($response == false) {
            throw new Exception("Failed to fetch data from GitHub API");
        }

        $data = json_decode($response, true);
        $latestVersion = $data['tag_name'];

        if ($this->lastKnownVersion !== $latestVersion) {
            $this->sendNotification();
        }
    }

    private function checkNetwork() {
        /* Check current height and version */
        $version = $this->monero->getVersion();
        $data = end($version['hard_forks']);
        $plannedHeight = $data['height'];
        $forkVersion = $data['hf_version'];

        $info = $this->monero->getInfo();
        $targetHeight = $info['target_height'];

        if ($targetHeight <= $plannedHeight && $this->lastHfVersion < $forkVersion) {
            $this->sendNotification();
        }
    }

    private function sendNotification() {
        /* Notification Email or Telegram */
    }
}