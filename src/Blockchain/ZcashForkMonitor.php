<?php
namespace App\Blockchain;

use App\Config\Env;
use App\Utils\Logger;
use App\Blockchain\Zcash;

class ZcashForkMonitor {
    private $lastSoftForkName;
    private $lastHardForkName;
    private $apiEndpoint = 'https://api.github.com/repos/zcash/zcash/releases/latest';
    private $configFile = '../zcash_config.json';
    private $zcash;
    private $logger;

    public function __construct() {
        $env = new Env(__DIR__ . '/../../.env');
        $env->load();
        $this->logger = new Logger(__DIR__ . '/../' . $env->get('LOG_PATH'), $env->get('LOG_LEVEL'));
        $this->loadConfig();
        $this->zcash = new Zcash(
            $env->get('ZCASH_URL'),
            $env->get('ZCASH_USERNAME'),
            $env->get('ZCASH_PASSWORD')
        );
    }

    private function loadConfig() {
        if (file_exists($this->configFile)) {
            $config = json_decode(file_get_contents($this->configFile), true);

            $this->lastSoftForkName = $config['last_soft_fork_name'] ?? '';
            $this->lastHardForkName = $config['last_hard_fork_name'] ?? '';
        } else {
            $this->saveConfig();
        }
    }

    private function saveConfig() {
        $config = [
            'last_soft_fork_name' => $this->lastSoftForkName,
            'last_hard_fork_name' => $this->lastHardForkName,
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

        $info = $this->zcash->getInfo();
        $build = $info['build'];

        if ($build !== $latestVersion) {
            $this->sendNotification();
        }
    }

    private function checkNetwork() {
        $blockchainInfo = $this->zcash->getBlockchainInfo();
        $softFork = end($blockchainInfo['softforks'])['id'];
        $hardfork = end($blockchainInfo['upgrades'])['name'];
        if ($this->lastHardForkName !== $hardfork || $this->lastSoftForkName !== $softFork) {
            $this->sendNotification();
        }
    }

    private function sendNotification() {
        /* Notification Email or Telegram */
    }
}