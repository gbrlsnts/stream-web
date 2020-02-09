<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Message\ResponseInterface as PsrResponse;

use App\Models\Stream;

use App\Traits\RedirectsUsers;
use App\Traits\ExtractsStreamFromRequest;

class RejectInvalidStream
{
    use RedirectsUsers;
    use ExtractsStreamFromRequest;

    /**
     * Invoke the middleware
     *
     * @param PsrRequest $request
     * @param PsrResponse $response
     * @return PsrResponse
     */
    public function __invoke(PsrRequest $request, PsrResponse $response, callable $next): PsrResponse
    {
        $stream = $this->getStreamName($request);

        try {
            (new Stream())->where('name', $stream)->firstOrFail();
        } catch(\Exception $e) {
            return $response->withStatus(404);
        }
        
        return $next($request, $response);
    }
}