<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Models\Stream;

use \Illuminate\Database\Eloquent\ModelNotFoundException;

return function (App $app) {
    $app->post('/origin/publish', function(Request $request, Response $response) {
        $streamName = $request->getParam('name');
        $token = $request->getParam('token');

        try {
            (new Stream())
                ->where('name', $streamName)
                ->where('token', $token)
                ->firstOrFail();
        } catch(ModelNotFoundException $e) {
            return $response->withStatus(403);
        }

        return $response->withStatus(200);
    });
};