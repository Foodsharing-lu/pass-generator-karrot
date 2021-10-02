<?php

namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerWrapper
{
    public const LOG_FILE_PATH = '../log/app.log';

    private const LOGGER_NAME = 'PassGeneratorLogger';

    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger($this::LOGGER_NAME);
        $this->logger->pushHandler(new StreamHandler($this::LOG_FILE_PATH, Logger::DEBUG));
    }

    public static function getLogger(): Logger
    {
        $loggerWrapper = new static();
        return $loggerWrapper->logger;
    }

    public static function info(string $message, array $context = []): void
    {
        $loggerWrapper = new static();
        $loggerWrapper->logger->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        $loggerWrapper = new static();
        $loggerWrapper->logger->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        $loggerWrapper = new static();
        $loggerWrapper->logger->warning($message, $context);
    }
}
