<?php

namespace JSONAPI\Routes;

use JSONAPI\App;
use JSONAPi\Data\Todo;
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


        if (isset($_GET['userId'])) {
            $data = $this->todo->getTodo(
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


        if (isset($_GET['completed'])) {
            $data = $this->todo->getTodo(
                completed: $_GET['completed'] == "true"
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


        if (!isset($_GET['userId']) && !isset($_GET['completed'])) {
            $totalData = $this->todo->getTodoCount();

            $paginationData = $this->app->preparePaginationResponse(
                totalItems: $totalData,
                currentPage: $_GET['page'] ?? 1,
                itemsPerPage: $_GET['limit'] ?? 10,
            );

            $data = $this->todo->getTodo(
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
