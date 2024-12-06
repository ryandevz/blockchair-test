<?php
namespace App\Blockchain;

use App\Blockchain\RPC;

class Monero extends RPC {

    public function getVersion(): array
    {
        return $this->call('get_version');
    }
}
