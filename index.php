<?php

require 'vendor/autoload.php';

use JSONAPI\App;
use JSONAPI\Utilities\RouteUtil;

$dotenv = Dotenv\Dotenv::createImmutable(paths: __DIR__);
$dotenv->load();

if ($_ENV['PRODUCTION'] == 1) {
    error_reporting(error_level: E_ALL);
    ini_set(option: 'ignore_repeated_errors', value: true);
    ini_set(option: 'display_errors', value: false);
    ini_set(option: "log_errors", value: 1);
    ini_set(option: "error_log", value: "src/logs/app.log");
}

header(header: 'Content-Type: application/json');
header(header: 'Access-Control-Allow-Origin: *');
header(header: 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header(header: 'Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$app = new App();

$router = new RouteUtil(app: $app);

//LOADS THE DATA FROM JSON INTO THE DATABASE
$router->addRoute(
    method: 'GET',
    path: '/initialize',
    controller: 'JSONAPI\Routes\InitRoute',
    action: 'loadData'
);

$router->addRoute(
    method: 'GET',
    path: '/users',
    controller: 'JSONAPI\Routes\UserRoute',
    action: 'getUsers'
);

$router->addRoute(
    method: 'GET',
    path: '/todos',
    controller: 'JSONAPI\Routes\TodoRoute',
    action: 'getTodos'
);

$router->addRoute(
    method: 'GET',
    path: '/posts',
    controller: 'JSONAPI\Routes\PostRoute',
    action: 'getPosts'
);

$router->addRoute(
    method: 'GET',
    path: '/comments',
    controller: 'JSONAPI\Routes\CommentRoute',
    action: 'getComments'
);

$router->addRoute(
    method: 'GET',
    path: '/albums',
    controller: 'JSONAPI\Routes\AlbumRoute',
    action: 'getAlbums'
);

$router->addRoute(
    method: 'GET',
    path: '/photos',
    controller: 'JSONAPI\Routes\PhotoRoute',
    action: 'getPhotos'
);

$router->handleRequest();
