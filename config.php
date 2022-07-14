<?php

//configuration
$site_config = [
    'install_enabled' => true,
    'url_base' => '/auth-system/',
    'db_username' => 'c0reversed',
    'db_password' => '!Gea5qKHepiZN',
    'db_connection' => 'mysql:host=localhost;dbname=c0reversed',
    'framework' => [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
    ]
];
