<?php

use Slim\App;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Bootstrap the database connection
 */
return function ($settings) {
    $capsule = new Capsule();

    $capsule->addConnection($settings);
    
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
};