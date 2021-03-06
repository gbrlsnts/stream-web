<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $container['view'] = function ($container) {
        $view = new \Slim\Views\Twig($container['settings']['renderer']['template_path'], [
            'cache' => $container['settings']['renderer']['cache_path']
        ]);
    
        $view->getEnvironment()->addGlobal('appname', $container['settings']['app']['name']);

        // Instantiate and add Slim specific extension
        $router = $container->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    
        return $view;
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // Auth Service
    $container['auth'] = function($c) {
        $settings = $c->get('settings')['app'];
        return new \App\Services\Auth($settings['password_algo']);
    };

    // Crypto Service
    $container['crypto'] = function($c) {
        $settings = $c->get('settings')['app'];

        $crypto = new \App\Services\Crypto($settings['encryption_key_path']);
        $crypto->boot();

        return $crypto;
    };

    // Token Service
    $container['token'] = function($c) {
        $crypto = $c->get('crypto');
        return new \App\Services\Token($crypto);
    };
};
