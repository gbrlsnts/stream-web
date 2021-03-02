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
var_dump($request);die;
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

        return $response->withRedirect('/s/'.$stream->name);
    });

    $app->post('/token/delete/{id}', function(Request $request, Response $response, array $args) {
        $user = $request->getAttribute('user');

        $token = (new Token)->with('stream')->findOrFail($args['id']);

        if(is_null($user) || $user->id !== $token->stream->id) {
            return $response->withStatus(403);
        }

        $token->delete();

        return $response->withRedirect('/s/'.$token->stream->name);
    });

};