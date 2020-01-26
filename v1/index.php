<?php

require '../libs/vendor/autoload.php';

$app = new Slim\App();

$app->post('/hello/{name}', function($request, $response, $args) {
    $name = $args['name'];
    $message = "Hello, " . $name . "!";
    return $response->write($message);
});

$app->run();

?>