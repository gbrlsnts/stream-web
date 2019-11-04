<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Middleware\NoAuthenticationRedirectToStream;
use App\Models\Stream;

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

        $stream = new Stream();
        $streamList = $stream->all();

        return $this->view->render($response, 'stream/stream-list.html', [
            'streams' => $streamList
        ]);
    })->add(new NoAuthenticationRedirectToStream($authService, $settings['app']['default_stream']));

    // Stream
    $app->get('/s/{stream}', function(Request $request, Response $response, array $args) use ($settings) {
        $user = $request->getAttribute('user');

        $playerSettings = $settings['player'];
        $isFlash = !is_null($request->getQueryParam('flash'));
        $streamName = $args['stream'];

        $stream = new Stream();
        $streamElement = $stream->where('name', $streamName)->first();

        return $this->view->render($response, 'stream/stream.html', [
            'stream' => $streamElement,
            'title' => $streamElement->name,
            'flashUrl' => format_stream_url($playerSettings['flash_url'], $args['stream']),
            'hlsUrl' => format_stream_url($playerSettings['hls_url'], $args['stream']),
            'techorder' => $isFlash ? $playerSettings['flash_techorder'] : $playerSettings['default_techorder'],
            'isOwner' => $streamElement->id === $_SESSION['user_id'], // stream id is the same as user id
        ]);
    });
};

