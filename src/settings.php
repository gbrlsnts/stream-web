<?php
return [
    'settings' => [
        'displayErrorDetails'       => true, // set to false in production
        'addContentLengthHeader'    => false, // Allow the web server to send the content-length header

        // App settings
        'app' => [
            'env'               => appenv('ENV', 'DEV'),
            'name'              => appenv('APP_NAME', 'Stream Demo'),
            'default_stream'    => appenv('DEFAULT_STREAM', 'ezstream'),
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache_path'    => appenv('ENV') !== 'DEV' ? '/tmp/slimcache' : false,
        ],

        // Monolog settings
        'logger' => [
            'name'  => 'stream-app',
            'path'  => 'php://stdout',
            'level' => appenv('ENV') === 'PROD' ? \Monolog\Logger::WARNING : \Monolog\Logger::DEBUG,
        ],

        // Player settings
        // Stream url must contain %stream% placeholder
        'player' => [
            'flash_url'         => appenv('STREAM_FLASH_URL', 'rtmp://localhost/stream/%stream%'),
            'hls_url'           => appenv('STREAM_HLS_URL', 'http://localhost/hls/%stream%.m3u8'),
            'flash_techorder'   => ['flash', 'html5'],
            'default_techorder' => ['html5'],
        ]
    ],
];
