<?php
namespace App\Blockchain;

use App\Blockchain\RPC;

class Monero extends RPC {

    public function getVersion(): array
    {
        return $this->call('get_version');
    }

    public function getInfo(): array
    {
        return $this->call('get_info');
    }

    public function getBlock($height): array
    {
        return $this->call('get_block', ['height' => $height]);
    }

    public function getTransaction(array $txs_hashes): array {
        return $this->callExtended('get_transactions', ['txs_hashes' => $txs_hashes, 'decode_as_json' => true]);
    }
}
