<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Message\ResponseInterface as PsrResponse;


use \Slim\Views\Twig;

class ShowResponseCode
{
    /**
     * Path to the templates folder, relative to the top templates folder
     *
     * @var string
     */
    private $templatesFolder = ''; 

    /**
     * View renderer
     *
     * @var Twig
     */
    private $view;

    /**
     * Initialize the middleware
     */
    public function __construct(Twig $view, $templatesFolder = 'responses/')
    {
        $this->templatesFolder = $templatesFolder;
        $this->view = $view;
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
        $resp = $next($request, $response);
        $code = $resp->getStatusCode();

        $view = $this->templatesFolder . $code . '.html';

        if($this->view->getLoader()->exists($view)) {
            return $this->view->render($resp, $view);
        }

        return $resp;
    }
}