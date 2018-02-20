<?php
namespace App\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashids\Hashids;

class UrlController
{

    protected $container;
    protected $url;

    public function __construct(Container $c)
    {
        $this->container = $c;
        $this->url = $this->container->get("db")->table("url");
    }

    public function index(Request $request, Response $response, array $args)
    {
        //短域名格式不匹配，返回404
        if (!preg_match('/^[a-zA-Z0-9]{6}$/', $args['url'])) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('404 Page not found');
        }
        //从数据库中查询完整域名并跳转
        $result = $this->url->where('url_short', $args['url'])->first();
        if ($result) {
            //click++
            $this->url->where('key', $result->key)->update(['click' => $result->click + 1]);
            return $response->withRedirect($result->url_full, 301);
        }
        //未查询到域名则跳转到首页
        else {
            return $response->withRedirect('/', 301);
        }

    }

    public function create($request, $response, $args)
    {
        $body = $request->getParsedBody();
        //trim去除首位空白字符
        $fullurl = trim($body['fullurl'], " \t\n\r\0\x0B/");
        //只允许(http|ftp)s?协议，输入时需要添加
        if (!empty($fullurl) && preg_match('/^(http|ftp)s?:\/\//', $fullurl)) {
            //查找数据库，若url已存在
            $isExist = $this->url->where('url_full', $fullurl)->first();
            if ($isExist) {
                return json_encode(array(
                    'status' => 'SUCCESS',
                    'id' => $isExist->key,
                    'url_s' => $this->container->get('settings')['domain'] . $isExist->url_short,
                    'url_f' => $isExist->url_full
                ));
            }
            //数据库中不存在，创建新的url
            $id = $this->url->insertGetId([
                'url_short' => 'notok',
                'url_full' => $fullurl
            ]);
            //生成hash为短链接
            $hashids = new Hashids($this->container->get('settings')['salt'], 6, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
            $shortened_url = $hashids->encode($id);
            if ($this->url->where('key', $id)->update(['url_short' => $shortened_url])) {
                return json_encode(array(
                    'status' => 'SUCCESS',
                    'id' => $id,
                    'url_s' => $this->container->get('settings')['domain'] . $shortened_url,
                    'url_f' => $fullurl
                ));
            }

        } else {
            return json_encode(array(
                'status' => 'ERROR',
                'msg' => 'Illegal url, please check your url again.'
            ));
        }

    }
}