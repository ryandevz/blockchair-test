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
        // $data = $this->database->fetchAll("SELECT * FROM blocks");

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