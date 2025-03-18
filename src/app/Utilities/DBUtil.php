<?php

namespace JSONAPI\Utilities;

use mysqli;
use Throwable;
use Monolog\Logger;
use mysqli_sql_exception;

class DBUtil
{
    protected mysqli $conn;

    public function __construct(
        public string $Host,
        public string $Username,
        public string  $Password,
        public string $Database,
        protected Logger $logger
    ) {
        try {
            $this->conn = mysqli_connect(
                hostname: $Host,
                username: $Username,
                password: $Password,
                database: $Database
            );
            $this->logger = $logger;
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

    public function getLastInsertedId(): int
    {
        return $this->conn->insert_id;
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

    public function getMultipleRecords($sql, $types = null, $params = []): array
    {
        try {
            $stmt = $this->conn->prepare($sql);
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

    public function closeConnection(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
