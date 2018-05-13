<?php
namespace App\Controller;

use Slim\Container;


class HomeController
{
    protected $container;
    protected $url;

    public function __construct(Container $c)
    {
        $this->container = $c;
        $this->url = $this->container->get("db")->table("url");
    }
    public function home($request, $response, $args)
    {
        //显示最近十个url
        // $urls = $this->url->orderBy('created_at', 'desc')
        //     ->take(5)
        //     ->get();

        $this->container->get('renderer')->render($response, 'home.phtml');
        return $response;
    }
}