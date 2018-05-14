<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/','\App\Controller\HomeController:home');
$app->post('/','\App\Controller\UrlController:create');
$app->get('/t','\App\Controller\UrlController:test');   //test router

$app->get('/s/{url}','\App\Controller\UrlController:redirectWithoutCache');
$app->get('/{url}', '\App\Controller\UrlController:redirect');
