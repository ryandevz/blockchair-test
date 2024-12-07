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
        $data = $this->database->fetchAll("SELECT * FROM monero_blocks limit 10");

        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => $data])
            ->send();
    }

    public function getZcash(Request $request, Response $response): void
    {
        $response->withStatus(200)
            ->withBody(['status' => 'success', 'data' => 'zcash'])
            ->send();
    }
}