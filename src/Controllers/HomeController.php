<?php
namespace App\Controller;

use Slim\Container;


class HomeController
{
    protected $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }
    public function home($request, $response, $args)
    {
        $pdo=$this->container->get('db');
        $stmt=$pdo->prepare("SELECT * FROM url");
        $stmt->execute();
        $result=$stmt->fetchAll();
        return json_encode($result);
        die();
        $this->container->get('renderer')->render($response, 'home.phtml', [
            'data' => $result
        ]);
        return $response;
    }
}