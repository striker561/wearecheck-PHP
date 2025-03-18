<?php

namespace JSONAPI\Routes;

use Exception;
use JSONAPI\App;
use JSONAPI\Data\Data;
use JSONAPI\Data\Post;
use JSONAPI\Data\Todo;
use JSONAPI\Data\User;
use JSONAPI\Data\Photo;
use JSONAPI\Data\Album;
use JSONAPI\Data\Comment;
use JSONAPI\Utilities\DBUtil;
use JSONAPI\Utilities\APIUtil;

class InitRoute
{
    private App $app;
    private DBUtil $db;
    private APIUtil $apiClient;


    private array $userMap = [];
    private array $postMap = [];
    private array $albumMap = [];
    private bool $userLoaded = false;
    private const API_BASE_URL = "https://jsonplaceholder.typicode.com/";

    public function __construct()
    {
        $this->db = new DBUtil();
        $this->app = new App();
        $this->apiClient = new APIUtil(
            endpoint: self::API_BASE_URL
        );
    }

    public function loadData(): void
    {
        try {
            if (!$this->loadUsers()) {
                throw new Exception(message: "Unable to load user data");
            }

            $this->userLoaded = true;

            if (!$this->loadTodos()) {
                throw new Exception(message: "Unable to load todo data");
            }

            if (!$this->loadPosts()) {
                throw new Exception(message: "Unable to load post data");
            }

            if (!$this->loadAlbums()) {
                throw new Exception(message: "Unable to load album data");
            }


            if (!$this->loadComments()) {
                throw new Exception(message: "Unable to load comments data");
            }


            if (!$this->loadPhotos()) {
                throw new Exception(message: "Unable to load photos data");
            }

            $this->clearMapping();

            $this->app->sendResponse(
                statusCode: 200,
                data: [
                    'message' => "Data Loaded successfully"
                ]
            );
        } catch (Exception $e) {
            $this->clearMapping();

            if ($this->userLoaded) {
                $this->cleanUp();
            }

            $this->app->sendResponse(
                statusCode: 400,
                data: [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    private function clearMapping(): void
    {
        $this->userMap = [];
        $this->postMap = [];
        $this->albumMap = [];
    }

    private function loadUsers(): bool
    {
        $response = $this->apiClient->get(
            path: "users"
        );
        if (!$response) {
            return false;
        }

        $dataToImport = [];

        foreach ($response as $user) {
            $ulid =  $this->app->getULID();
            $this->userMap[$user['id']] = $ulid;

            $dataToImport[] = [
                $ulid,
                $user['name'],
                $user['username'],
                json_encode(value: $user['address']),
                $user['phone'],
                $user['website'],
                json_encode(value: $user['company'])
            ];
        }

        //unset response
        unset($response);

        $user = new User(db: $this->db);
        return $user->saveMultipleUsers(
            users: $dataToImport
        );
    }

    private function loadTodos(): bool
    {
        $response = $this->apiClient->get(
            path: 'todos'
        );
        if (!$response) {
            return false;
        }

        $dataToImport = [];
        foreach ($response as $todo) {
            $dataToImport[] = [
                $this->app->getULID(),
                $this->userMap[$todo["userId"]],
                $todo['title'],
                $todo['completed']
            ];
        }

        //unset response
        unset($response);

        $todo = new Todo(db: $this->db);
        return $todo->saveMultipleTodos(
            todos: $dataToImport
        );
    }

    private function loadPosts(): bool
    {
        $response = $this->apiClient->get(
            path: "posts"
        );
        if (!$response) {
            return false;
        }

        $dataToImport = [];
        foreach ($response as $post) {
            $ulid =  $this->app->getULID();
            $this->postMap[$post['id']] = $ulid;

            $dataToImport[] = [
                $ulid,
                $this->userMap[$post['userId']],
                $post['title'],
                $post['body']
            ];
        }

        //unset response
        unset($response);

        $post = new Post(db: $this->db);
        return $post->saveMultiplePosts(
            posts: $dataToImport
        );
    }

    private function loadAlbums(): bool
    {
        $response = $this->apiClient->get(
            path: "albums"
        );
        if (!$response) {
            return false;
        }

        $dataToImport = [];
        foreach ($response as $album) {
            $ulid =  $this->app->getULID();

            $this->albumMap[$album['id']] = $ulid;

            $dataToImport[] = [
                $ulid,
                $this->userMap[$album['userId']],
                $album['title']
            ];
        }

        //unset response
        unset($response);

        $album = new Album(db: $this->db);
        return $album->saveMultipleAlbums(
            albums: $dataToImport
        );
    }

    private function loadComments(): bool
    {
        $response = $this->apiClient->get(
            path: "comments"
        );
        if (!$response) {
            return false;
        }

        $dataToImport = [];
        foreach ($response as $comment) {
            $dataToImport[] = [
                $this->app->getULID(),
                $this->postMap[$comment['postId']],
                $comment['name'],
                $comment['email'],
                $comment['body']
            ];
        }

        //unset response
        unset($response);

        $comment = new Comment(db: $this->db);
        return $comment->saveMultipleComments(
            comments: $dataToImport
        );
    }

    private function loadPhotos(): bool
    {
        $response = $this->apiClient->get(
            path: "photos"
        );
        if (!$response) {
            return false;
        }

        $dataToImport = [];
        foreach ($response as $photo) {
            $dataToImport[] = [
                $this->app->getULID(),
                $this->albumMap[$photo['albumId']],
                $photo['title'],
                $photo['url'],
                $photo['thumbnailUrl']
            ];
        }

        //unset response
        unset($response);

        $photo = new Photo(db: $this->db);
        return $photo->saveMultiplePhotos(
            photos: $dataToImport
        );
    }

    private function cleanUp(): void
    {
        // deleting the user datable will delete the other data since thats how it was setup
        $data = new Data(db: $this->db);
        $data->deleteAllRow(tableName: 'tbl_user');
    }
}
