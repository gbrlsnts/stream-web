<?php

use Slim\App;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Bootstrap the database connection
 */
return function (App $app) {
    $container = $app->getContainer();

    $capsule = new Capsule();

    $capsule->addConnection($container['settings']['database']);
    
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
};