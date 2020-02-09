<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Middleware\NoAuthenticationRedirectToStream;
use App\Middleware\AccessTokenPrivateStream;
use App\Middleware\RejectInvalidStream;
use App\Models\Stream;

return function (App $app) {
    $container = $app->getContainer();
    $settings = $container->get('settings');
    $router = $app->getContainer()->get('router');

    $authService = $container->get('auth');
    $tokenService = $container->get('token');

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

        $streamList = (new Stream())->all();

        return $this->view->render($response, 'stream/stream-list.html', [
            'streams' => $streamList
        ]);
    })->setName('stream-list')->add(new NoAuthenticationRedirectToStream($authService, $settings['app']['default_stream']));

    // Stream
    $app->get('/s/{stream}', function(Request $request, Response $response, array $args) use ($settings) {
        $user = $request->getAttribute('user');

        $playerSettings = $settings['player'];
        $isFlash = !is_null($request->getQueryParam('flash'));
        $streamName = $args['stream'];

        $streamElement = (new Stream())
            ->with('tokens')
            ->where('name', $streamName)
            ->orderBy('created_at', 'desc')
            ->first();

        return $this->view->render($response, 'stream/stream.html', [
            'stream' => $streamElement,
            'title' => $streamElement->name,
            'streamAbsoluteUrl' => $settings['app']['app_url'] . '/s/' . $args['stream'],
            'flashUrl' => format_stream_url($playerSettings['flash_url'], $args['stream']),
            'hlsUrl' => format_stream_url($playerSettings['hls_url'], $args['stream']),
            'techorder' => $isFlash ? $playerSettings['flash_techorder'] : $playerSettings['default_techorder'],
            'isOwner' => $streamElement->id === $_SESSION['user_id'], // stream id is the same as user id
        ]);
    })->setName('stream')
        ->add(new AccessTokenPrivateStream($router, $authService, $tokenService))
        ->add(new RejectInvalidStream);

    $app->post('/s/{stream}/lock', function(Request $request, Response $response, array $args) {
        $user = $request->getAttribute('user');
        $stream = (new Stream)->where('name', $args['stream'])->firstOrFail();

        if(is_null($user) || $user->id !== $stream->id) {
            return $response->withStatus(403);
        }

        $stream->is_private = 1;
        $stream->save();

        return $response->withRedirect('/s/'.$stream->name);
    })->setName('stream-lock');

    $app->post('/s/{stream}/unlock', function(Request $request, Response $response, array $args) {
        $user = $request->getAttribute('user');
        $stream = (new Stream)->where('name', $args['stream'])->firstOrFail();

        if(is_null($user) || $user->id !== $stream->id) {
            return $response->withStatus(403);
        }

        $stream->is_private = 0;
        $stream->save();

        return $response->withRedirect('/s/'.$stream->name);
    })->setName('stream-unlock');
};

