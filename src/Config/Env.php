<?php
namespace App\Config;

class Env {
    private $path;
    private $variables = [];
    
    public function __construct(string $path = null) {
        $this->path = $path ?? dirname(__DIR__) . '/.env';
    }
    
    public function load(): void {
        if (!file_exists($this->path)) {
            throw new RuntimeException(sprintf('Environment file not found at: %s', $this->path));
        }

        if (!is_readable($this->path)) {
            throw new RuntimeException(sprintf('Environment file is not readable: %s', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            /* Skip comments */
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse variable
            if (strpos($line, '=') !== false) {
                list($name, $value) = $this->parseLine($line);
                
                if (!empty($name)) {
                    $this->variables[$name] = $value;
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
    
    private function parseLine(string $line): array {
        /* Split line at first equals sign */
        $parts = explode('=', $line, 2);
        
        if (count($parts) !== 2) {
            return [null, null];
        }
        
        $name = trim($parts[0]);
        $value = trim($parts[1]);
        
        // Remove quotes if they exist
        if (strlen($value) > 1) {
            $first = substr($value, 0, 1);
            $last = substr($value, -1);
            
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        
        /* Expand variables */
        $value = $this->expandVariables($value);
        
        return [$name, $value];
    }
    
    private function expandVariables(string $value): string {
        /* Replace ${VAR} or $VAR with actual values */
        return preg_replace_callback('/\${([a-zA-Z0-9_]+)}|\$([a-zA-Z0-9_]+)/', function($matches) {
            $var = $matches[1] ?? $matches[2];
            return getenv($var) ?: (isset($this->variables[$var]) ? $this->variables[$var] : '');
        }, $value);
    }
    
    public function get(string $name) {
        return $this->variables[$name] ?? null;
    }
    
    public function getAllVariables(): array {
        return $this->variables;
    }
}