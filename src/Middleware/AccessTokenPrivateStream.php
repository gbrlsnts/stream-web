<?php

namespace App\Middleware;

use Slim\App;
use Slim\Router;

use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Message\ResponseInterface as PsrResponse;

use App\Services\Auth;
use App\Services\Token;
use App\Models\Stream;

use App\Traits\RedirectsUsers;

class AccessTokenPrivateStream
{
    use RedirectsUsers; 

    /**
     * Slim router
     *
     * @var Router
     */
    protected $router;

    /**
     * Auth service
     *
     * @var Auth
     */
    protected $authService;

    /**
     * Token service
     *
     * @var Token
     */
    protected $tokenService;

    /**
     * Initialize the middleware
     *
     * @param Router $router
     * @param Auth $authService
     * @param Token $tokenService
     */
    public function __construct(Router $router, Auth $authService, Token $tokenService)
    {
        $this->router = $router;
        $this->authService = $authService;
        $this->tokenService = $tokenService;
    }

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
        $token = $this->tokenService->getStreamTokenFromRequest($request, $stream);

        if ($this->isAuthorized($stream, $token)) {
            return $next($request->withAttribute('view_authorized', true), $response);
        }

        return $this->redirectUser($request, $response, $this->createRedirectPath($stream));
    }

    /**
     * Check if the current user is authorized to access the stream
     *
     * @param string $token
     * @param string $streamName
     * @return boolean
     */
    protected function isAuthorized(string $streamName, string $token): bool
    {
        // Allow other registered users to access private streams
        if ($this->authService->isAuthenticated()) {
            return true;
        }

        $userId = $this->authService->getAuthenticatedUserId();
        $stream = (new Stream)->where('name', $streamName)->firstOrFail();

        return $this->tokenService->isTokenValid($stream->id, $token);
    }

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

    /**
     * Create the path to redirect to
     *
     * @param string $streamName
     * @return string
     */
    protected function createRedirectPath(string $streamName): string
    {
        return $this->router->pathFor('token-use', [
            'stream' => $streamName,
        ]);
    }
}