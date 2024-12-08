<?php
namespace App\Controllers;

use App\Config\Env;
use App\Utils\Logger;
use App\Database\DatabaseConnection;
use App\Http\Request;
use App\Http\Response;

class DataController
{
    private $env;
    private $logger;
    private $database;

    public function __construct(Env $env, Logger $logger, DatabaseConnection $database) {
        $this->env = $env;
        $this->logger = $logger;
        $this->database = $database;
    }

    public function getMonero(Request $request, Response $response): void
    {
        $data = $this->database->fetchAll("SELECT * FROM monero_blocks limit 100");

        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => $data])
            ->send();
    }

    public function getZcash(Request $request, Response $response): void
    {
        $data = $this->database->fetchAll("SELECT * FROM zcash_blocks limit 100");

        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => $data])
            ->send();
    }

    public function moneroBlock(Request $request, Response $response): void
    {
        $id = $request->getRouteParam('id');
        $data = $this->database->fetchOne("SELECT * FROM monero_blocks where height = ?", [$id]);

        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => $data])
            ->send();
    }

    public function zcashBlock(Request $request, Response $response): void
    {
        $id = $request->getRouteParam('id');
        $data = $this->database->fetchOne("SELECT * FROM zcash_blocks where tx_hash = ?", [$id]);

        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => $data])
            ->send();
    }

    public function moneroTransaction(Request $request, Response $response): void
    {
        $id = $request->getRouteParam('id');
        $data = $this->database->fetchOne("SELECT * FROM monero_transactions where height = ?", [$id]);

        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => $data])
            ->send();
    }

    public function zcashTransaction(Request $request, Response $response): void
    {
        $id = $request->getRouteParam('id');
        $data = $this->database->fetchOne("SELECT * FROM zcash_transactions where txid = ?", [$id]);

        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => $data])
            ->send();
    }
}