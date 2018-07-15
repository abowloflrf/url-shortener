<?php
namespace App\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Hashids\Hashids;
use Illuminate\Database\Capsule\Manager as DB;

class UrlController
{

    protected $container;
    protected $url;
    protected $mc;

    public function __construct(Container $c)
    {
        $this->container = $c;
        $this->url = $this->container->get("db")->table("url");
        $this->mc = $this->container->get('memcached');
    }

    //使用Mamcached作为缓存
    public function redirect(Request $request, Response $response, array $args)
    {
        //短域名格式不匹配，返回404
        if (!preg_match('/^[a-zA-Z0-9]{6}$/', $args['url'])) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('404 Page not found');
        }
        //从缓存中查找
        $inCache = $this->mc->get('url_' . $args['url']);

        if ($inCache) {
            return $response->withRedirect($inCache, 301);
        } else {
            //从数据库中查询完整域名并跳转
            $result = $this->url->where('url_short', $args['url'])->first();
            if ($result) {
                //click++
                //$this->url->where('key', $result->key)->update(['click' => $result->click + 1]);
                //种缓存
                $this->mc->set('url_' . $args['url'], $result->url_full);
                //跳转到目标链接
                return $response->withRedirect($result->url_full, 301);
            }
            //未查询到域名则跳转到首页
            else {
                return $response->withRedirect('/', 301);
            }
        }
    }

    //不适用缓存，每次直接查询数据库作跳转，用作对比性能测试
    public function redirectWithoutCache(Request $request, Response $response, array $args)
    {
        //短域名格式不匹配，返回404
        if (!preg_match('/^[a-zA-Z0-9]{6,}$/', $args['url'])) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write('404 Page not found');
        }
        //从数据库中查询完整域名并跳转
        $result = $this->url->where('url_short', $args['url'])->first();
        if ($result) {
            //click++
            //$this->url->where('key', $result->key)->update(['click' => $result->click + 1]);
            //跳转到目标链接
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
            if (strpos($fullurl, $this->container->get('settings')['domain']) !== false) {
                return json_encode(array(
                    'status' => 'ERROR',
                    'msg' => '请不要输入本网站地址'
                ));
            }
            //这里不再查询数据库长链接是否已存在，因为长连接并没有设置索引，数据过多时会降低性能
            //且后续会增加用户统计功能，确保用户每次生成的短链接确实这个用户生成的单独为其统计数据
            // $isExist = $this->url->where('url_full', $fullurl)->first();
            // if ($isExist) {
            //     return json_encode(array(
            //         'status' => 'SUCCESS',
            //         'id' => $isExist->key,
            //         'url_s' => $this->container->get('settings')['domain'] . '/' . $isExist->url_short,
            //         'url_f' => $isExist->url_full,
            //         'is_new' => false
            //     ));
            // }
            //$this->container->get('settings')['domain'] . '/' . $shortened_url
            $shorturl = $this->newUrl($fullurl);

            if ($shorturl != '0')
                return json_encode(array(
                'status' => 'SUCCESS',
                'hashid' => $shorturl,
                'url_s' => $this->container->get('settings')['domain'] . '/' . $shorturl,
                'url_f' => $fullurl
            ));
            else
                return json_encode(array(
                'status' => 'ERROR',
                'msg' => '生成短链接失败'
            ));
        } else {
            return json_encode(array(
                'status' => 'ERROR',
                'msg' => 'URL不合法，请重试'
            ));
        }

    }

    private function newUrl(string $fullurl)
    {
        //获取mysql下一个自增ID
        $statement = DB::select("SHOW TABLE STATUS LIKE 'url'");
        $nextId = $statement[0]->Auto_increment;
        //生成hash为短链接
        $hashids = new Hashids($this->container->get('settings')['salt'], 6, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $shortened_url = $hashids->encode($nextId);
        //插入记录
        if ($this->url->insert([
            'url_short' => $shortened_url,
            'url_full' => $fullurl
        ]))
            return $shortened_url;
        return "0";
    }

    public function test()
    {
        $str = $this->mc->get('test_string');
        return $str;
    }
}