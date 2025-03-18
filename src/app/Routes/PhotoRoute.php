<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPI\Data\Photo;
use JSONAPI\Utilities\DBUtil;

class PhotoRoute
{

    private App $app;
    private DBUtil $db;
    private Photo $photo;

    public function __construct()
    {
        $this->app = new App();
        $this->db = new DBUtil();
        $this->photo = new Photo(db: $this->db);
    }


    public function getPhotos(): void
    {
        $response = [];


        $totalData = $this->photo->getPhotoCount(
            albumId: (isset($_GET['albumId'])) ? $_GET['albumId'] : null,
        );

        $paginationData = $this->app->preparePaginationResponse(
            totalItems: $totalData,
            currentPage: $_GET['page'] ?? 1,
            itemsPerPage: $_GET['limit'] ?? 10,
        );

        $data = $this->photo->getPhoto(
            albumId: (isset($_GET['albumId'])) ? $_GET['albumId'] : null,
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
