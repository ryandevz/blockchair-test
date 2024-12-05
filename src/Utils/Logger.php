<?php
namespace App\Utils;

/**
 * PSR-3 Logger Interface
 */
interface LoggerInterface
{
    public function emergency($message, array $context = array());
    public function alert($message, array $context = array());
    public function critical($message, array $context = array());
    public function error($message, array $context = array());
    public function warning($message, array $context = array());
    public function notice($message, array $context = array());
    public function info($message, array $context = array());
    public function debug($message, array $context = array());
    public function log($level, $message, array $context = array());
}

/**
 * PSR-3 Log Levels
 */
class LogLevel
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    /* Array to define level hierarchy (higher number = more severe) */
    private static $levels = [
        self::DEBUG     => 0,
        self::INFO      => 1,
        self::NOTICE    => 2,
        self::WARNING   => 3,
        self::ERROR     => 4,
        self::CRITICAL  => 5,
        self::ALERT     => 6,
        self::EMERGENCY => 7,
    ];

    public static function isValidLevel($level)
    {
        return array_key_exists($level, self::$levels);
    }

    public static function compare($a, $b)
    {
        return self::$levels[$a] - self::$levels[$b];
    }
}

/**
 * Logger implementing PSR-3 LoggerInterface
 */
class Logger implements LoggerInterface
{
    private $logPath;
    private $minLevel;
    
    public function __construct($logPath = 'logs/app.log', $minLevel = LogLevel::DEBUG)
    {
        if (!LogLevel::isValidLevel($minLevel)) {
            throw new InvalidArgumentException('Invalid log level');
        }
        
        $this->logPath = $logPath;
        $this->minLevel = $minLevel;
        
        // Create logs directory if it doesn't exist
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    public function setMinLevel($level)
    {
        if (!LogLevel::isValidLevel($level)) {
            throw new InvalidArgumentException('Invalid log level');
        }
        $this->minLevel = $level;
    }

    public function getMinLevel()
    {
        return $this->minLevel;
    }
    
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }
    
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }
    
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }
    
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }
    
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }
    
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }
    
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }
    
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
    
    public function log($level, $message, array $context = array())
    {
        /* Check if we should log this level */
        if (LogLevel::compare($level, $this->minLevel) < 0) {
            return; // Skip logging if below minimum level
        }

        /* Replace placeholders in message with context values */
        $message = $this->interpolate($message, $context);
        
        /* Format log entry */
        $logEntry = sprintf(
            '[%s] %s: %s%s',
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            PHP_EOL
        );
        
        /* Write to log file */
        file_put_contents(
            dirname(__DIR__) . '/' . $this->logPath,
            $logEntry,
            FILE_APPEND | LOCK_EX
        );
    }
    
    private function interpolate($message, array $context = array())
    {
        /* Build a replacement array with braces around the context keys */
        $replace = array();
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        
        /* Interpolate replacement values into the message and return */
        return strtr($message, $replace);
    }
}