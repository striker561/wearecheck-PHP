<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPi\Data\Comment;
use JSONAPI\Utilities\DBUtil;

class CommentRoute
{
    private App $app;
    private DBUtil $db;
    private Comment $comment;

    public function __construct()
    {
        $this->app = new App();
        $this->db = new DBUtil();
        $this->comment = new Comment(db: $this->db);
    }


    public function getComments(): void
    {
        $response = [];

        if (!isset($_GET['postId'])) {
            $this->app->sendResponse(
                statusCode: 40,
                data: [
                    'error' => "Enter a post id"
                ]
            );
        }

        $totalData = $this->comment->getCommentCount(
            postId: $_POST['postId']
        );

        $paginationData = $this->app->preparePaginationResponse(
            totalItems: $totalData,
            currentPage: $_GET['page'] ?? 1,
            itemsPerPage: $_GET['limit'] ?? 10,
        );

        $data = $this->comment->getComment(
            postId: $_POST['postId'],
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
