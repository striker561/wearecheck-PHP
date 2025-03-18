<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPI\Data\Todo;
use JSONAPI\Utilities\DBUtil;

class TodoRoute
{
    private App $app;
    private DBUtil $db;
    private Todo $todo;

    public function __construct()
    {

        $this->app = new App();
        $this->db = new DBUtil();
        $this->todo = new Todo(db: $this->db);
    }

    public function getTodos(): void
    {
        $response = [];

        $totalData = $this->todo->getTodoCount(
            userId: (isset($_GET['userId'])) ? $_GET['userId'] : null,
            completed: (isset($_GET['completed'])) ? $_GET['completed'] == "true" : null,
        );

        $paginationData = $this->app->preparePaginationResponse(
            totalItems: $totalData,
            currentPage: $_GET['page'] ?? 1,
            itemsPerPage: $_GET['limit'] ?? 10,
        );

        $data = $this->todo->getTodo(
            userId: (isset($_GET['userId'])) ? $_GET['userId'] : null,
            completed: (isset($_GET['completed'])) ? $_GET['completed'] == "true" : null,
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
