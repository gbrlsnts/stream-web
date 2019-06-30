<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function(Request $request, Response $response, array $args){
        // Render index view
        return $this->view->render($response, 'index.html');
    });

    $app->get('/s/{stream}', function(Request $request, Response $response, array $args) {
        return $this->view->render($response, 'index.html', [
            'name' => $args['stream']
        ]);
    });
};
