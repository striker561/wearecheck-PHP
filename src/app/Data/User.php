<?php

namespace JSONAPi\Data;

class User extends Data
{

    public function saveUser(
        string $ulid,
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
            param: [$ulid, $name, $username, $address, $phone, $website, $company]
        );
    }

    public function getUser(
        int $limit = 10,
        int $offset = 0,
    ): ?array {
        return $this->db->getMultipleRecords(
            query: "SELECT FROM tbl_user LIMIT ? OFFSET ?",
            types: 'ii',
            params: [$limit, $offset]
        );
    }
}
