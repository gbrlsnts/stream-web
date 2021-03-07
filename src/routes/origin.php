<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

use App\Models\Stream;

return function (App $app) {
    $app->post('/origin/publish', function(Request $request, Response $response) {
        $streamName = $request->getParam('name');
        $token = $request->getParam('token');

        try {
            $stream = (new Stream())
                ->where('name', $streamName)
                ->where('token', $token)
                ->firstOrFail();

            $stream->is_streaming = true;
            $stream->last_stream_at = Carbon::now();

            $stream->save();
        } catch(ModelNotFoundException $e) {
            return $response->withStatus(403);
        }

        return $response->withStatus(200);
    });

    $app->post('/origin/publish-done', function(Request $request, Response $response) {
        $streamName = $request->getParam('name');

        try {
            $stream = (new Stream())
                ->where('name', $streamName)
                ->firstOrFail();

            $stream->is_streaming = false;

            $stream->save();
        } catch(ModelNotFoundException $e) {
            return $response->withStatus(404);
        }

        return $response->withStatus(200);
    });
};