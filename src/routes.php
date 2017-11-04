<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/','\App\Controller\HomeController:home');
$app->post('/','\App\Controller\HomeController:create');
$app->get('/{url}', '\App\Controller\UrlController:index');
