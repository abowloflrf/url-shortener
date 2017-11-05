<?php
namespace App\Controller;

use Slim\Container;
use App\Utils\UrlTrans;


class HomeController
{
    protected $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }
    public function home($request, $response, $args)
    {
        $pdo = $this->container->get('db');
        $stmt = $pdo->prepare("SELECT * FROM url LIMIT 10");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $this->container->get('renderer')->render($response, 'home.phtml', [
            'urls' => $result
        ]);
        return $response;
    }

    public function create($request, $response, $args)
    {
        //TODO: 判断url是否已经在数据库存在若存在直接返回短链接
        $body = $request->getParsedBody();
        //trim去除首位空白字符
        $fullurl = trim($body['fullurl'], " \t\n\r\0\x0B/") . '/';
        //只允许(http|ftp)s?协议，输入时需要添加
        if (!empty($fullurl) && preg_match('/^(http|ftp)s?:\/\//', $fullurl)) {
            //获取表中的auto_increment
            $pdo = $this->container->get('db');
            $stmt = $pdo->prepare("SELECT auto_increment FROM information_schema.`TABLES` WHERE TABLE_SCHEMA='url-shortener' AND TABLE_NAME='url'");
            $stmt->execute();
            $id = (int)$stmt->fetch()['auto_increment'];
            //将auto_increment转为62进制作为短域名
            $shortened_url = UrlTrans::IDtoURL($id);
            $stmt_insert = $pdo->prepare("INSERT INTO url (url_short,url_full) VALUES (:url_short,:url_full)");
            $stmt_insert->bindParam(':url_short', $shortened_url);
            $stmt_insert->bindParam(':url_full', $fullurl);
            $stmt_insert->execute();
            return json_encode(array(
                'status' => 'success',
                'id' => $id,
                'url_s' => $this->container->get('settings')['domain'].$shortened_url,
                'url_f' => $fullurl
            ));
        }
        else {
            return json_encode(array(
                'status' => 'fail',
                'msg' => 'Illegal url, please check your url again.'
            ));
        }

    }
}