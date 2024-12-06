<?php
namespace App\Database;

use PDO;

class DatabaseConnection {
    private $connection;
    private static $instance = null;
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $password;

    private function __construct($config = []) {
        $this->host = $config['host'] ?? 'localhost';
        $this->port = $config['port'] ?? '5432';
        $this->dbname = $config['dbname'] ?? 'postgres';
        $this->user = $config['user'] ?? 'postgres';
        $this->password = $config['password'] ?? 'postgres';
        
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            $this->connection = new PDO($dsn, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance($config = []) {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection($config);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
}