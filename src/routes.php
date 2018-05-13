<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/','\App\Controller\HomeController:home');
$app->post('/','\App\Controller\UrlController:create');
$app->get('/t','\App\Controller\UrlController:test');
$app->get('/{url}', '\App\Controller\UrlController:index');
