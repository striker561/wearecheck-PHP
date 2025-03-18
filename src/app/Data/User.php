<?php

namespace JSONAPI\Data;

class User extends Data
{

    public function saveUser(
        string $id,
        string $name,
        string $username,
        string $address,
        string $phone,
        string $website,
        string $company
    ): bool {
        return $this->db->insertSingleRecord(
            query: "INSERT INTO tbl_user SET id = ?, name = ?, username = ?, address = ?, phone = ?, website = ?, company = ?",
            types: 'sssssss',
            param: [$id, $name, $username, $address, $phone, $website, $company]
        );
    }

    public function saveMultipleUsers(
        array $users
    ): bool {
        return $this->db->insertBulkRecords(
            query: "INSERT INTO tbl_user (id, name, username, address, phone, website, company) VALUES (?, ?, ?, ?, ?, ?, ?)",
            types: 'sssssss',
            paramSets: $users
        );
    }

    public function getUserCount(): int
    {
        return $this->db->getSingleRecord(
            query: "SELECT COUNT(id) AS userCount FROM tbl_user WHERE id != ?",
            types: 'i',
            param: [0]
        )['userCount'];
    }

    public function getUser(
        ?string $userId = null,
        int $limit = 10,
        int $offset = 0,
    ): ?array {
        if ($userId) {
            return $this->db->getSingleRecord(
                query: "SELECT * FROM tbl_user WHERE id = ?",
                types: 's',
                param: [$userId]
            );
        }
        return $this->db->getMultipleRecords(
            query: "SELECT * FROM tbl_user LIMIT ? OFFSET ?",
            types: 'ii',
            params: [$limit, $offset]
        );
    }
}
