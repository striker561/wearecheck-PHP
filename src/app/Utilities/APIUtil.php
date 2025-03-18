<?php

namespace JSONAPI\Utilities;

use Monolog\Level;
use Monolog\Logger;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Exception\RequestException;

class APIUtil
{
    private Client $client;
    protected Logger $logger;
    public function __construct(
        private string $endpoint,
    ) {
        $this->client = new Client();

        $this->logger = new Logger(name: 'db_logger');
        $this->logger->pushHandler(
            handler: new StreamHandler(
                stream: __DIR__ . '/../',
                level: Level::Debug
            )
        );
    }

    private function getPath(?string $path): string
    {
        return "{$this->endpoint}{$path}";
    }

    public function get(?string $path = null): ?array
    {
        try {
            $response = $this->client->get(uri: $this->getPath(path: $path));
            return $this->parseResponse(response: $response);
        } catch (RequestException $e) {
            $this->handleException(e: $e);
            return null;
        }
    }

    public function post(array $data, ?string $path = null): ?array
    {
        try {
            $response = $this->client->post(uri: $this->getPath(path: $path), options: ['json' => $data]);
            return $this->parseResponse(response: $response);
        } catch (RequestException $e) {
            $this->handleException(e: $e);
            return null;
        }
    }

    private function parseResponse(\Psr\Http\Message\ResponseInterface $response): mixed
    {
        $body = $response->getBody()->getContents();
        return json_decode(json: $body, associative: true);
    }

    private function handleException(RequestException $e)
    {
        if ($this->logger) {
            $this->logger->error(message: 'Error: ' . $e->getMessage() . PHP_EOL);
        }
    }
}
