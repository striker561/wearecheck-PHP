<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPI\Data\User;
use JSONAPI\Utilities\DBUtil;

class UserRoute
{

    private App $app;
    private DBUtil $db;
    private User $user;

    public function __construct()
    {
        $this->app = new App();
        $this->db = new DBUtil();
        $this->user = new User(db: $this->db);
    }


    public function getUsers(): void
    {
        $response = [];

        if (isset($_GET['userId'])) {
            $data = $this->user->getUser(
                userId: $_GET['userId']
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

        if (!isset($_GET['userId'])) {
            $totalData = $this->user->getUserCount();

            $paginationData = $this->app->preparePaginationResponse(
                totalItems: $totalData,
                currentPage: $_GET['page'] ?? 1,
                itemsPerPage: $_GET['limit'] ?? 10,
            );

            $data = $this->user->getUser(
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
