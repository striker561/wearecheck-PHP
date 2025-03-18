<?php

namespace JSONAPI;

use Ulid\Ulid;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class App
{

    public function __construct() {}

    public function getAppUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = "$protocol://$host/";
        return $baseUrl;
    }


    public function getULID(): string
    {
        return Ulid::generate();
    }

    public function createLogger(
        string $name,
        string $logFilePath,
        Level $level
    ): Logger {
        $logger = new Logger(name: $name);
        $logger->pushHandler(
            handler: new StreamHandler(
                stream: $logFilePath,
                level: $level
            )
        );
        return $logger;
    }

    public function sendResponse($statusCode = 200, mixed $data): never
    {
        http_response_code(response_code: $statusCode);
        echo json_encode(value: $data);
        exit;
    }
}
