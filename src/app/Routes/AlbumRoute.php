<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPI\Data\Album;
use JSONAPI\Utilities\DBUtil;

class AlbumRoute
{

    private App $app;
    private DBUtil $db;
    private Album $album;

    public function __construct()
    {
        $this->app = new App();
        $this->db = new DBUtil();
        $this->album = new Album(db: $this->db);
    }


    public function getAlbums(): void
    {
        $response = [];

        $totalData = $this->album->getAlbumCount(
            userId: (isset($_GET['userId'])) ? $_GET['userId'] : null,);

        $paginationData = $this->app->preparePaginationResponse(
            totalItems: $totalData,
            currentPage: $_GET['page'] ?? 1,
            itemsPerPage: $_GET['limit'] ?? 10,
        );

        $data = $this->album->getAlbum(
            userId: (isset($_GET['userId'])) ? $_GET['userId'] : null,
            limit: $paginationData['itemPerPage'],
            offset: $paginationData['offset'],
        );

        $response = [
            'items' => $data,
            'pagination' => $paginationData,
        ];

        $this->app->sendResponse(
            statusCode: 200,
            data: $response
        );
    }
}
