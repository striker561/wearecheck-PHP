<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPi\Data\Post;
use JSONAPI\Utilities\DBUtil;


class PostRoute
{
    private App $app;
    private DBUtil $db;
    private Post $post;

    public function __construct()
    {
        $this->app = new App();
        $this->db = new DBUtil();
        $this->post = new Post(db: $this->db);
    }

    public function getPosts(): void
    {

        $response = [];

        if (isset($_GET['userId'])) {
            $data = $this->post->getPost(
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
            $totalData = $this->post->getPostCount();

            $paginationData = $this->app->preparePaginationResponse(
                totalItems: $totalData,
                currentPage: $_GET['page'] ?? 1,
                itemsPerPage: $_GET['limit'] ?? 10,
            );

            $data = $this->post->getPost(
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
