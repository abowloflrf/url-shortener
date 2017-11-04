<?php
namespace App\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class UrlController
{

    protected $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    public function index(Request $request, Response $response, array $args)
    {
        //TODO:处理短域名解析并跳转
        return $args['url'];
    }
}