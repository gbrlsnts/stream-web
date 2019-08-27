<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $authService = $app->getContainer()->get('auth');

    $app->get('/login', function(Request $request, Response $response) {
        return $this->view->render($response, 'auth/login.html');
    });

    $app->post('/login', function(Request $request, Response $response) use ($authService) {
        $success = $authService->authenticate(
            $request->getParsedBodyParam('username'), 
            $request->getParsedBodyParam('password')
        );

        if ($success) {
            return $response->withRedirect('/s');
        }

        return $this->view->render($response, 'auth/login.html', [
            'hasError' => true
        ]);
    });

    $app->get('/logout', function(Request $request, Response $response) use ($authService) {
        $authService->logout();

        return $response->withRedirect('/s');
    });
};

