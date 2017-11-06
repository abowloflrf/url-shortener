<?php
namespace App\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Utils\UrlTrans;

class UrlController
{

    protected $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    public function index(Request $request, Response $response, array $args)
    {
        //短域名格式不匹配，返回404
        if (!preg_match('/^[a-zA-Z0-9]{4,6}$/', $args['url'])) {
            return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('404 Page not found');
        }
        //从数据库中查询完整域名并跳转
        $pdo = $this->container->get('db');
        $stmt = $pdo->prepare("SELECT url_full FROM url WHERE url_short ='" . $args['url'] . "'");
        //TODO: 统计点击量/refer
        $stmt->execute();
        $result = $stmt->fetch();
        //跳转
        if ($result) {
            return $response->withRedirect($result['url_full'], 301);
        }
        //未查询到域名则跳转到首页
        else {
            return $response->withRedirect('/', 301);
        }

    }
}