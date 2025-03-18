<?php

namespace JSONAPi\Data;

class Photo extends Data
{

    public function savePhoto(
        string $id,
        string $albumId,
        string $title,
        string $url,
        string $thumbnailUrl
    ): bool {
        return $this->db->insertSingleRecord(
            query: "INSERT INTO tbl_photo SET id = ?, albumId = ?, title = ?, url = ?, thumbnailUrl = ?",
            types: 'sssss',
            param: [$id, $albumId, $title, $url, $thumbnailUrl]
        );
    }

    public function saveMultiplePhotos(
        array $photos
    ): bool {
        return $this->db->insertBulkRecords(
            query: "INSERT INTO tbl_photo SET id = ?, albumId = ?, title = ?, url = ?, thumbnailUrl = ?",
            types: 'sssss',
            paramSets: $photos
        );
    }

    public function getPhoto(
        ?string $albumId = null,
        int $limit = 10,
        int $offset = 0,
    ): ?array {
        $query = "SELECT * FROM tbl_photo";
        $types = '';
        $param = [];

        if ($albumId) {
            $query .= " WHERE albumId = ?";
            $types .= 's';
            $param[] = $albumId;
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
