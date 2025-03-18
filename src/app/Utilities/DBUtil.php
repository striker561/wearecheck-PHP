<?php

namespace JSONAPI\Utilities;

use Exception;
use mysqli;
use Throwable;
use Monolog\Level;
use Monolog\Logger;
use mysqli_sql_exception;
use Monolog\Handler\StreamHandler;

class DBUtil
{
    protected mysqli $conn;
    protected Logger $logger;

    public function __construct()
    {
        try {
            $this->logger = new Logger(name: 'db_logger');
            $this->logger->pushHandler(
                handler: new StreamHandler(
                    stream: __DIR__ . '/../../logs/db.log',
                    level: Level::Debug
                )
            );

            $this->conn = mysqli_connect(
                hostname: $_ENV['DATABASE_HOST'],
                username: $_ENV['DATABASE_USER'],
                password: $_ENV['DATABASE_PASSWORD'],
                database: $_ENV['DATABASE_NAME']
            );
            if ($this->conn->connect_error) {
                $error_msg = "Unable to connect to the database: " . $this->conn->connect_error;
                throw new mysqli_sql_exception(message: $error_msg);
            }
        } catch (mysqli_sql_exception $e) {
            $this->logger->error(message: $e);
        }
    }

    public function getConnectionString(): mysqli
    {
        return $this->conn;
    }

    public function getSingleRecord($query, $types, $param): array
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$param);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data ?? [];
        } catch (Throwable $th) {
            $this->logger->error(message: "Error executing query : {$th}");
            return [];
        }
    }

    public function getMultipleRecords($query, $types = null, $params = []): array
    {
        try {
            $stmt = $this->conn->prepare($query);
            if (!empty($params) && !empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $data ?? [];
        } catch (Throwable $th) {
            $this->logger->error(message: "Error executing query : {$th}");
            return [];
        }
    }

    public function insertSingleRecord($query, $types, $param): bool
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$param);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (Throwable $th) {
            $this->logger->error(message: "Error executing query : {$th}");
            return false;
        }
    }

    public function insertBulkRecords($query, $types, $paramSets): bool
    {
        try {
            $placeholdersPerRow = substr_count(haystack: $query, needle: '?');

            if (strlen(string: $types) != $placeholdersPerRow) {
                throw new Exception(message: "Types string length doesn't match placeholder count");
            }

            $queryParts = explode(separator: 'VALUES', string: $query, limit: 2);

            if (count(value: $queryParts) != 2) {
                throw new Exception(message: "Query must contain 'VALUES' keyword");
            }
            $query = $queryParts[0] . 'VALUES ';
            $placeholderPart = trim(string: $queryParts[1]);


            $allPlaceholders = [];
            for ($i = 0; $i < count(value: $paramSets); $i++) {
                $allPlaceholders[] = $placeholderPart;
            }

            $query .= implode(separator: ', ', array: $allPlaceholders);

            $allParams = [];
            foreach ($paramSets as $params) {
                foreach ($params as $param) {
                    $allParams[] = $param;
                }
            }
            $allTypes = str_repeat(string: $types, times: count(value: $paramSets));

            $stmt = $this->conn->prepare(query: $query);
            $stmt->bind_param($allTypes, ...$allParams);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Throwable $th) {
            $this->logger->error(message: "Error executing bulk insert: {$th}");
            return false;
        }
    }

    public function closeConnection(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
