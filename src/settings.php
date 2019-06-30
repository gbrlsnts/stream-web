<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // App settings
        'app' => [
            'env' => env('ENV', 'DEV'),
            'name' => env('APP_NAME', 'Stream Demo'),
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache_path' => env('ENV') === 'PROD' ? '/tmp/slimcache' : false,
        ],

        // Monolog settings
        'logger' => [
            'name' => 'stream-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Player settings
        // Stream url must contain %stream% placeholder
        'player' => [
            'flash_url' => env('STREAM_FLASH_URL', 'rtmp://rn.barricas.rocks/stream/%stream%'),
            'hls_url' => env('STREAM_HLS_URL', '//rn.barricas.rocks/hls/%stream%.m3u8'),
        ]
    ],
];
