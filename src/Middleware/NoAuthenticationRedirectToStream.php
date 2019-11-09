<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Message\ResponseInterface as PsrResponse;

use App\Models\User;
use App\Services\Auth;

use App\Traits\RedirectsUsers;

class NoAuthenticationRedirectToStream
{
    use RedirectsUsers;

    /**
     * Auth service
     *
     * @var Auth
     */
    protected $authService;

    /**
     * Default stream name
     *
     * @var string
     */
    protected $defaultStream;

    /**
     * Initialize the middleware
     *
     * @param Auth $authService
     * @param string $defaultStream
     */
    public function __construct(Auth $authService, string $defaultStream)
    {
        $this->authService = $authService;
        $this->defaultStream = $defaultStream;
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
        if ($this->authService->isAuthenticated()) {
            return $next($request, $response);
        }

        return $this->redirectUser($request, $response, $this->createRedirectPath());
    }

    /**
     * Create the path to redirect to
     *
     * @return string
     */
    protected function createRedirectPath(): string
    {
        return '/s/' . $this->defaultStream;
    }
}