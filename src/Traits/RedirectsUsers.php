<?php

namespace App\Traits;

use Slim\Http\Response;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Message\ResponseInterface as PsrResponse;

/**
 * Trait to redirect users to a page in the app
 */
trait RedirectsUsers
{
    /**
     * Creates a response with a redirect
     *
     * @param PsrRequest $request
     * @param PsrResponse $response
     * @param string $to
     * @return Response
     */
    protected function redirectUser(PsrRequest $request, PsrResponse $response, string $to): PsrResponse
    {   
        $response = new Response();

        return $response->withRedirect($to);
    }
}
