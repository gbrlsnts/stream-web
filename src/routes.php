<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    // Index
    $app->get('/', function(Request $request, Response $response, array $args) {
        return $this->view->render($response, 'index.html');
    });

    // Stream routes
    $stream = require __DIR__ . '/routes/stream.php';
    $stream($app);

    // Auth routes
    $auth = require __DIR__ . '/routes/auth.php';
    $auth($app);
};
