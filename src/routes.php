<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    // Index
    $app->get('/', function(Request $request, Response $response, array $args) {
        // Users that not have the permission to view all streams should be redirected to the default one
        return $response->withRedirect('/s/' . $container['settings']['app']['default_stream']);
    });

    // Stream list aliases
    $app->redirect('/stream', '/s', 301);
    $app->redirect('/streams', '/s', 301);

    // Stream list
    $app->get('/s', function(Request $request, Response $response, array $args) use ($container) {
        return $this->view->render($response, 'stream/stream-list.html');
    });

    // Stream
    $app->get('/s/{stream}', function(Request $request, Response $response, array $args) use ($container) {
        $playerSettings = $container['settings']['player'];
        $isFlash = !is_null($request->getQueryParam('flash'));

        return $this->view->render($response, 'stream/stream.html', [
            'stream' => $args['stream'],
            'title' => $args['stream'],
            'flash_url' => format_stream_url($playerSettings['flash_url'], $args['stream']),
            'hls_url' => format_stream_url($playerSettings['hls_url'], $args['stream']),
            'techorder' => $isFlash ? $playerSettings['flash_techorder'] : $playerSettings['default_techorder'],
        ]);
    });
};
