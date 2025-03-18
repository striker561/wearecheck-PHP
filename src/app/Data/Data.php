<?php

namespace JSONAPI\Data;

use JSONAPI\Utilities\DBUtil;

class Data
{
    public function __construct(protected DBUtil $db) {}

    protected function getSingleColumn(
        string $tableName,
        string $columnName,
        string $columnValue,
        string $types = "s",
        string $returnColumns = "*",
    ): ?array {
        return $this->db->getSingleRecord(
            query: "SELECT $returnColumns FROM $tableName WHERE $columnName = ?",
            types: $types,
            param: [$columnValue]
        );
    }


    protected function updateSingleColumn(
        string $tableName,
        string $whereColumn,
        string $whereValue,
        string $columnName,
        string $columnValue,
        string $types = 'ss',
    ): bool {
        return $this->db->insertSingleRecord(
            query: "UPDATE $tableName SET $columnName = ? WHERE $whereColumn = ?",
            types: $types,
            param: [$columnValue, $whereValue]
        );
    }


    protected function deleteRow(
        string $tableName,
        string $columnName,
        string $columnValue,
        string $types = "s",
    ): bool {
        return $this->db->insertSingleRecord(
            query: "DELETE FROM $tableName WHERE $columnName = ?",
            types: $types,
            param: [$columnValue]
        );
    }
}
