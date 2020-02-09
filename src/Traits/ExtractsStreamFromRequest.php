<?php

namespace App\Traits;

use Psr\Http\Message\ServerRequestInterface as PsrRequest;

/**
 * Trait extract stream from requests
 */
trait ExtractsStreamFromRequest
{
    /**
     * Extract the stream name from the request object
     *
     * @param PsrRequest $request
     * @return string
     */
    protected function getStreamName(PsrRequest $request): string
    {
        $routeInfo = $request->getAttribute('routeInfo');

        if(count($routeInfo) === 0)
            return '';

        $params =  $routeInfo[2];
        
        if(count($params) === 0)
            return '';

        return $params['stream'] ?? '';
    }
}
