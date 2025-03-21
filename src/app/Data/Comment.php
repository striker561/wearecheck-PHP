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
            query: "INSERT INTO tbl_comment (id, postId, name, email, body) VALUES (?, ?, ?, ?, ?)",
            types: 'sssss',
            paramSets: $comments
        );
    }

    public function getCommentCount(string $postId): int
    {
        return $this->db->getSingleRecord(
            query: "SELECT COUNT(id) AS commentCount FROM tbl_comment WHERE postId = ?",
            types: 's',
            param: [$postId]
        )['commentCount'];
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
