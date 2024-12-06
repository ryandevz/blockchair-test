<?php
namespace App\Blockchain;

use App\Blockchain\RPC;

class Zcash extends RPC {

    public function getBlockchainInfo(): array
    {
        return $this->call('getblockchaininfo');
    }
}