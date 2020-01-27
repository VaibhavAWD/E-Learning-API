<?php

require '../libs/vendor/autoload.php';
require '../include/util/Helper.php';
require '../include/db/DbOperations.php';
require '../include/controller/AuthController.php';
require '../include/controller/UserController.php';

$app = new Slim\App();

$message = array();

$user_id = null;

$app->post('/hello/{name}', function($request, $response, $args) {
    $name = $args['name'];
    $message = "Hello, " . $name . "!";
    return $response->write($message);
});

$app->post('/conncheck', function($request, $response, $args) {
    require_once '../include/db/DbConnect.php';
    $db = new DbConnect();
    $conn = $db->connect();
    if ($conn != null) {
        $message = "Database connection established successfully!";
        return $response->write($message);
    }
});

/* ---------------------------------------------- USERS API ---------------------------------------------- */

$app->post('/register', \UserController::class . ':register');

$app->post('/login', \UserController::class . ':login');

$app->put('/users', \UserController::class . ':update')->add(\AuthController::class);

$app->put('/deactivate', \UserController::class . ':deactivate')->add(\AuthController::class);

/* ---------------------------------------------- USERS API ---------------------------------------------- */

$app->run();

?>