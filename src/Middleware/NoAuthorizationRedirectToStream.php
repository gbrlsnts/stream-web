<?php

namespace App\Middleware;

use Slim\App;
use Slim\Http\Response;

use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Server\RequestHandlerInterface as PsrRequestHandler;

use App\Models\User;

class NoAuthorizationRedirectToStream {
    
    protected $defaultStream;

    public function __construct(App $app)
    {
        $container = $app->getContainer();

        $this->defaultStream = $container['settings']['app']['default_stream'];
    }

    /**
     * Invoke the middlware
     */
    public function __invoke(PsrRequest $request, PsrRequestHandler $handler): PsrResponse
    {
        $loggedUser = $_SESSION['user_id'];

        if (!\is_null($loggedUser)) {
            return $this->handleResponse($request, $handler);
        }

        return $this->redirectUser($request, $handler, $this->createRedirectPath());
    }

    protected function redirectUser(PsrRequest $request, PsrRequestHandler $handler, string $to): PsrResponse
    {   
        $response = new Response();

        return $response->withRedirect($to);
    }

    protected function handleResponse(PsrRequest $request, PsrRequestHandler $handler): PsrResponse
    {
        return $handler->handle($request);
    }

    protected function createRedirectPath(): string
    {
        return '/s/' . $this->defaultStream;
    }
}