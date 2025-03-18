<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPi\Data\Photo;
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

        if (isset($_GET['albumId'])) {
            $data = $this->photo->getPhoto(
                albumId: $_GET['albumId']
            );
            if (!$data) {
                $this->app->sendResponse(
                    statusCode: 40,
                    data: [
                        'error' => "Record not found"
                    ]
                );
            }
            $response = $data;
        }

        if (!isset($_GET['albumId'])) {
            $totalData = $this->photo->getPhotoCount();

            $paginationData = $this->app->preparePaginationResponse(
                totalItems: $totalData,
                currentPage: $_GET['page'] ?? 1,
                itemsPerPage: $_GET['limit'] ?? 10,
            );

            $data = $this->photo->getPhoto(
                limit: $paginationData['itemPerPage'],
                offset: $paginationData['offset'],
            );

            $response = [
                'items' => $data,
                'pagination' => $paginationData,
            ];
        }

        $this->app->sendResponse(
            statusCode: 200,
            data: $response
        );
    }
}
