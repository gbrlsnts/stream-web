<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Middleware\NoAuthenticationRedirectToStream;
use App\Middleware\ShowResponseCode;

return function (App $app) {
    $container = $app->getContainer();

    $view = $app->getContainer()->get('view');
    $settings = $container->get('settings');
    $authService = $container->get('auth');

    // Index
    $app->get('/', function(Request $request, Response $response, array $args) {
        return $this->view->render($response, 'index.html');
    })->add(new NoAuthenticationRedirectToStream($authService, $settings['app']['default_stream']));

    // Stream routes
    $stream = require __DIR__ . '/routes/stream.php';
    $stream($app);

    // Auth routes
    $auth = require __DIR__ . '/routes/auth.php';
    $auth($app);

    // Token routes
    $token = require __DIR__ . '/routes/token.php';
    $token($app);

    // Origin routes
    $origin = require __DIR__ . '/routes/origin.php';
    $origin($app);

    // Catch all errors view (if it exists)
    $app->add(new ShowResponseCode($view));
};
