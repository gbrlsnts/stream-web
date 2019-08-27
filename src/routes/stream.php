<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Middleware\NoAuthenticationRedirectToStream;

return function (App $app) {
    $container = $app->getContainer();
    $settings = $container->get('settings');
    $authService = $container->get('auth');

    // Stream list aliases
    $app->redirect('/stream', '/s', 301);
    $app->redirect('/streams', '/s', 301);

    // Stream list
    $app->get('/s', function(Request $request, Response $response, array $args) use ($settings) {
        $user = $request->getAttribute('user');

        // todo: place into middleware
        if (is_null($user) || !$user->can_list_streams) {
            return $response->withRedirect('/s/' . $settings['app']['default_stream']);
        }

        return $this->view->render($response, 'stream/stream-list.html');
    })->add(new NoAuthenticationRedirectToStream($authService, $settings['app']['default_stream']));

    // Stream
    $app->get('/s/{stream}', function(Request $request, Response $response, array $args) use ($settings) {
        $playerSettings = $settings['player'];
        $isFlash = !is_null($request->getQueryParam('flash'));

        return $this->view->render($response, 'stream/stream.html', [
            'stream' => $args['stream'],
            'title' => $args['stream'],
            'flashUrl' => format_stream_url($playerSettings['flash_url'], $args['stream']),
            'hlsUrl' => format_stream_url($playerSettings['hls_url'], $args['stream']),
            'techorder' => $isFlash ? $playerSettings['flash_techorder'] : $playerSettings['default_techorder'],
        ]);
    });
};

