<?php
namespace App\Blockchain;

use App\Blockchain\RPC;

class Zcash extends RPC {

    public function getBlockchainInfo(): array
    {
        return $this->call('getblockchaininfo');
    }

    public function getInfo(): array
    {
        return $this->call('getinfo');
    }

    public function getBlock($height): array
    {
        return $this->call('getblock', [$height]);
    }

    public function getTransaction(array $params): array {
        return $this->call('gettransaction', $params);
    }
}