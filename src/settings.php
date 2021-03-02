<?php
return [
    'settings' => [
        'displayErrorDetails'       => true, // set to false in production
        'addContentLengthHeader'    => false, // Allow the web server to send the content-length header

        // App settings
        'app' => [
            'env'                   => appenv('ENV', 'DEV'),
            'name'                  => appenv('APP_NAME', 'Stream Demo'),
            'app_url'               => appenv('APP_URL', 'http://localhost'),
            'default_stream'        => appenv('DEFAULT_STREAM', 'ezstream'),
            'default_stream_token'  => appenv('DEFAULT_STREAM_TOKEN', 'ezstream'),
            'default_password'      => appenv('DEFAULT_PASSWORD', 'ezstream'),
            'password_algo'         => appenv('PASSWORD_ALGO') ?: PASSWORD_DEFAULT,
            'token_size'            => appenv('TOKEN_SIZE', 5),
            'secure_link_secret'    => appenv('SECURE_LINK_SECRET', 'secret'),
            'secure_link_ttl'       => appenv('SECURE_LINK_TTL', 3600),
            'trusted_proxy'         => appenv('TRUSTED_PROXY', '172.16.0.0/12'),
            'encryption_key_path'   => absolute_path('data/crypto.key'),
        ],

        // Database settings
        'database' => [
            'driver'                        => 'sqlite',
            'database'                      => absolute_path('data/app.sqlite3'),
            'charset'                       => 'utf8',
            'collation'                     => 'utf8_unicode_ci',
            'foreign_key_constraints'       => true,
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
            'hls_url'           => appenv('STREAM_HLS_URL', 'http://localhost/hls/%stream%.m3u8?expires=%expire%&token=%token%'),
            'flash_techorder'   => ['chromecast', 'flash', 'html5'],
            'default_techorder' => ['chromecast', 'html5'],
        ]
    ],
];
