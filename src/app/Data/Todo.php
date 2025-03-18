<?php

namespace JSONAPI\Data;

class Todo extends Data
{
    public function saveTodo(
        string $id,
        string $userId,
        string $title,
        bool $completed
    ): bool {
        return $this->db->insertSingleRecord(
            query: "INSERT INTO tbl_todo SET id = ?, userId = ?, title = ?, completed = ?",
            types: 'sssi',
            param: [$id, $userId, $title, $completed]
        );
    }

    public function saveMultipleTodos(
        array $todos
    ): bool {
        return $this->db->insertBulkRecords(
            query: "INSERT INTO tbl_todo (id, userId, title, completed) VALUES (?, ?, ?, ?)",
            types: 'sssi',
            paramSets: $todos
        );
    }

    public function getTodoCount(
        ?string $userId = null,
        ?bool $completed = null
    ): int {
        $query = "SELECT COUNT(id) AS todoCount FROM tbl_todo";
        $types = '';
        $param = [];


        if ($userId) {
            $query .= " WHERE userId = ?";
            $types .= 's';
            $param[] = $userId;
        } else {
            $query .= " WHERE id != ?";
            $types .= 'i';
            $param[] = 0;
        }

        if ($completed !== null) {
            $query .= " AND completed = ?";
            $types .= 'i';
            $param[] = $completed ? 1 : 0;
        }

        return $this->db->getSingleRecord(
            query: $query,
            types: $types,
            param: $param
        )['todoCount'];
    }


    public function getTodo(
        ?string $userId = null,
        ?bool $completed = null,
        int $limit = 10,
        int $offset = 0,
    ): ?array {
        $query = "SELECT * FROM tbl_todo";
        $types = '';
        $param = [];
        $whereAdded = false;

        if ($userId) {
            $query .= " WHERE userId = ?";
            $types .= 's';
            $param[] = $userId;
            $whereAdded = true;
        }

        if ($completed !== null) {
            if ($whereAdded) {
                $query .= " AND completed = ?";
            } else {
                $query .= " WHERE completed = ?";
                $whereAdded = true;
            }
            $types .= 'i';
            $param[] = $completed ? 1 : 0;
        }

        $query .= " LIMIT ? OFFSET ?";
        $types .= "ii";
        $param[] = $limit;
        $param[] = $offset;

        return $this->db->getMultipleRecords(
            query: $query,
            types: $types,
            params: $param
        );
    }
}
