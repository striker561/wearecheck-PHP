<?php

namespace JSONAPI\Data;

class Album extends Data
{

    public function saveAlbum(
        string $id,
        string $userId,
        string $title,
    ): bool {
        return $this->db->insertSingleRecord(
            query: "INSERT INTO tbl_album SET id = ?, userId = ?, title = ?",
            types: 'sss',
            param: [$id, $userId, $title]
        );
    }

    public function saveMultipleAlbums(
        array $albums
    ): bool {
        return $this->db->insertBulkRecords(
            query: "INSERT INTO tbl_album SET id = ?, userId = ?, title = ?",
            types: 'sss',
            paramSets: $albums
        );
    }


    public function getAlbum(
        ?string $userId = null,
        int $limit = 10,
        int $offset = 0,
    ): ?array {
        $query = "SELECT * FROM tbl_album";
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
