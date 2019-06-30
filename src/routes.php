<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/', function(Request $request, Response $response, array $args){
        // Render index view
        return $this->view->render($response, 'index.html');
    });

    $app->get('/s/{stream}', function(Request $request, Response $response, array $args) use ($container) {
        return $this->view->render($response, 'stream/stream.html', [
            'stream' => $args['stream'],
            'title' => $args['stream'],
            'flash_url' => format_stream_url($container['settings']['player']['flash_url'], $args['stream']),
            'hls_url' => format_stream_url($container['settings']['player']['hls_url'], $args['stream']),
        ]);
    });
};
