<?php

require '../libs/vendor/autoload.php';

$app = new Slim\App();

$message = array();

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

$app->post('/helper', function($request, $response, $args) {
    require_once '../include/util/Helper.php';

    if (!Helper::hasRequiredParams(array('email'), $response)) {
        return;
    }

    $data = $request->getParams();
    $email = $data['email'];

    if (!Helper::isValidEmail($email, $response)) {
        return;
    }

    $message[Helper::ERROR] = false;
    $message[Helper::MESSAGE] = "Email: " . $email;
    return Helper::buildResponse(Helper::STATUS_OK, $message, $response);
});

$app->run();

?>