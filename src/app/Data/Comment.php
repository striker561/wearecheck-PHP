<?php

namespace JSONAPI\Data;

class Comment extends Data
{

    public function saveComment(
        string $id,
        string $postId,
        string $name,
        string $email,
        string $body
    ): bool {
        return $this->db->insertSingleRecord(
            query: "INSERT INTO tbl_comment SET id = ?, postId = ?, name = ?, email = ?, body = ?",
            types: 'sssss',
            param: [$id, $postId, $name, $email, $body]
        );
    }


    public function saveMultipleComments(
        array $comments
    ): bool {
        return $this->db->insertBulkRecords(
            query: "INSERT INTO tbl_comment SET id = ?, postId = ?, name = ?, email = ?, body = ?",
            types: 'sssss',
            paramSets: $comments
        );
    }

    public function getComment(
        string $postId,
        int $limit = 10,
        int $offset = 0
    ): ?array {
        return $this->db->getMultipleRecords(
            query: "SELECT * FROM tbl_comment WHERE postId = ? LIMIT ? OFFSET ?",
            types: 'sii',
            params: [$postId, $limit, $offset]
        );
    }
}
