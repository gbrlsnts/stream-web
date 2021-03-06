<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Models\Stream;
use App\Models\Token;
use Carbon\Carbon;

return function (App $app) {
    $container = $app->getContainer();
    $settings = $container->get('settings');
    $tokenService = $container->get('token');

    $app->get('/token/use/{stream}', function(Request $request, Response $response, array $args) {
        $stream = (new Stream)->where('name', $args['stream'])->firstOrFail();

        return $this->view->render($response, 'token/use-token.html', [
            'stream' => $stream,
        ]);
    })->setName('token-use');

    $app->post('/token/try/{stream}', function(Request $request, Response $response, array $args) use ($tokenService) {
        $stream = (new Stream)->where('name', $args['stream'])->firstOrFail();

        $token = $request->getParsedBodyParam('token', '');

        if($tokenService->isTokenValid($stream->id, $token)) {
            $tokenService->incrementTokenUsages($stream->id, $token);

            return $tokenService
                ->addTokenToResponse($stream->name, $token, $response)
                ->withRedirect('/s/'.$stream->name);
        }

        return $this->view->render($response, 'token/use-token.html', [
            'stream' => $stream,
            'hasError' => true,
        ]);
    })->setName('token-try');

    $app->post('/token/{stream}', function(Request $request, Response $response, array $args) use ($settings, $tokenService) {
        $user = $request->getAttribute('user');

        $stream = (new Stream)->where('name', $args['stream'])->first();
        
        if(is_null($user) || $user->id !== $stream->id) {
            return $response->withStatus(403);
        }

        $body = $request->getParsedBody();

        $tokenService->generateToken(
            $stream->id,
            $settings['app']['token_size'],
            $body['description'],
            intval($body['max_usages']),
            Carbon::parse($body['expires_at'])
        );

        return $response->withRedirect('/token/manage/'.$stream->name);
    });

    $app->post('/token/delete/{id}', function(Request $request, Response $response, array $args) {
        $user = $request->getAttribute('user');

        $token = (new Token)->with('stream')->findOrFail($args['id']);

        if(is_null($user) || $user->id !== $token->stream->id) {
            return $response->withStatus(403);
        }

        $token->delete();

        return $response->withRedirect('/token/manage/'.$token->stream->name);
    });

    $app->post('/token/delete-bulk/{stream}', function(Request $request, Response $response, array $args) {
        $user = $request->getAttribute('user');
        $stream = (new Stream)->where('name', $args['stream'])->firstOrFail();

        if(is_null($user) || $user->id !== $stream->id) {
            return $response->withStatus(403);
        }

        (new Token)->where('stream_id', $stream->id)->delete();

        return $response->withRedirect('/token/manage/'.$stream->name);
    });

    $app->get('/token/manage/{stream}', function(Request $request, Response $response, array $args) use ($settings) {
        $user = $request->getAttribute('user');
        $stream = (new Stream)->where('name', $args['stream'])->firstOrFail();

        if(is_null($user) || $user->id !== $stream->id) {
            return $response->withStatus(403);
        }

        $tokens = (new Token)
            ->where('stream_id', $stream->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->view->render($response, 'token/manage-stream.html', [
            'stream' => $stream,
            'tokens' => $tokens,
            'title' => $stream->name . ': Manage Access Tokens',
            'streamAbsoluteUrl' => $settings['app']['app_url'] . '/s/' . $args['stream'],
        ]);
    });

};