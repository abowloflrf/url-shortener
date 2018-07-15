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
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        //db config
        // 'db'=>[
        //     'host'=>'127.0.0.1:3306',
        //     'user'=>'root',
        //     'pass'=>'',
        //     'dbname'=>'url-shortener',
        // ],
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'url-shortener',
            'username' => 'root',
            'password' => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        //
        'domain'=>'http://127.0.0.1',
        'salt'=>'urlshortener'
    ],
];
