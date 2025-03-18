<?php

namespace JSONAPI\Data;

class Post extends Data
{

    public function savePost(
        string $id,
        string $userId,
        string $title,
        string $body,
    ): bool {
        return $this->db->insertSingleRecord(
            query: "INSERT INTO tbl_post SET id = ?, userId = ?, title = ?, body = ?",
            types: 'ssss',
            param: [$id, $userId, $title, $body]
        );
    }

    public function saveMultiplePosts(
        array $posts
    ): bool {
        return $this->db->insertBulkRecords(
            query: "INSERT INTO tbl_post (id, userId, title, body) VALUES (?, ?, ?, ?)",
            types: 'ssss',
            paramSets: $posts
        );
    }


    public function getPostCount(
        ?string $userId = null,
    ): int {
        $query = "SELECT COUNT(id) AS postCount FROM tbl_post";
        $types = '';
        $param = [];

        if($userId){
            $query .= " WHERE userId = ?";
            $types .= 's';
            $param[] = $userId;
        } else {
            $query .= " WHERE id != ?";
            $types .= 'i';
            $param[] = 0;
        }

        return $this->db->getSingleRecord(
            query: $query,
            types: $types,
            param: $param
        )['postCount'];
    }

    public function getPost(
        ?string $userId = null,
        int $limit = 10,
        int $offset = 0
    ): ?array {
        $query = "SELECT * FROM tbl_post";
        $types = '';
        $param = [];

        if ($userId) {
            $query .= " WHERE userId = ?";
            $types .= 's';
            $param[] = $userId;
        };

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
