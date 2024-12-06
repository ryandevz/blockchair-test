<?php
namespace App\Controllers;

use App\Database\DatabaseConnection;
use App\Http\Request;
use App\Http\Response;

class DataController
{
    public function getMonero(Request $request, Response $response): void
    {
        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => 'monero'])
            ->send();
    }

    public function getZcash(Request $request, Response $response): void
    {
        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => 'zcash'])
            ->send();
    }
}