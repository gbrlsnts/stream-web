<?php

use Slim\App;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use RKA\Middleware\IpAddress;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Middleware\ShowResponseCode;

return function (App $app) {
    $container = $app->getContainer();

    $settings = $container->get('settings');
    $view = $container->get('view');

    // Add trailing slashes
    $app->add(function (Request $request, Response $response, callable $next) {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));
            
            if ($request->getMethod() == 'GET') {
                return $response->withRedirect((string) $uri, 301);
            }
            else {
                return $next($request->withUri($uri), $response);
            }
        }
    
        return $next($request, $response);
    });

    // Inject authenticated user to request and template data
    $app->add(function (Request $request, Response $response, callable $next) use ($app) {
        $container = $app->getContainer();

        $authService = $container->get('auth');
        $viewService = $container->get('view');

        $user = null;

        if ($authService->isAuthenticated()) {
            $user = $authService->getAuthenticatedUser();
        }

        $request = $request->withAttribute('user', $user);
        $viewService->getEnvironment()->addGlobal('authUser', $user);
    
        return $next($request, $response);
    });

    // Catch model not found and return 404
    $app->add(function (Request $request, Response $response, callable $next) {
        try {
            return $next($request, $response);
        } catch(ModelNotFoundException $e) {
            return $response->withStatus(404);
        }
    });

    // Catch all errors view (if it exists)
    $app->add(new ShowResponseCode($view));

    // Get trusted client ip address
    $headersToInspect = [
        'CF-Connecting-IP',
        'True-Client-IP',
        'Forwarded',
        'X-Forwarded-For',
        'X-Forwarded',
        'X-Cluster-Client-Ip',
        'Client-Ip',
    ];

    $checkHeaders = $settings['app']['env'] !== 'DEV';

    $app->add(new IpAddress(
        $checkHeaders,
        get_trusted_proxies($settings),
        null,
        $headersToInspect
    ));
};