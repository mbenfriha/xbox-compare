<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'upload' => [
          'public_path' => __DIR__ . '/../public/upload/game/'
        ],

        "db" => [
            "host" => "localhost",
            "dbname" => "xbox",
            "user" => "root",
            "pass" => "root"
        ],
    ],
];
