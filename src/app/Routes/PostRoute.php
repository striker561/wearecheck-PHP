<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPI\Data\Post;
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

        $totalData = $this->post->getPostCount(
            userId: (isset($_GET['userId'])) ? $_GET['userId'] : null,
        );

        $paginationData = $this->app->preparePaginationResponse(
            totalItems: $totalData,
            currentPage: $_GET['page'] ?? 1,
            itemsPerPage: $_GET['limit'] ?? 10,
        );

        $data = $this->post->getPost(
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
