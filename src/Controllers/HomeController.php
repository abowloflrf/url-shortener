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
        $stmt = $pdo->prepare("SELECT * FROM url");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $this->container->get('renderer')->render($response, 'home.phtml', [
            'urls' => $result
        ]);
        return $response;
    }

    public function create($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $fullurl=$body['fullurl'];

        $pdo = $this->container->get('db');
        $stmt = $pdo->prepare("SELECT auto_increment FROM information_schema.`TABLES` WHERE TABLE_SCHEMA='url-shortener' AND TABLE_NAME='url'");
        $stmt->execute();
        $id=(int)$stmt->fetch()['auto_increment'];
        $shortened_url=UrlTrans::IDtoURL($id);
        $stmt_insert = $pdo->prepare("INSERT INTO url (url_short,url_full) VALUES (:url_short,:url_full)");
        $stmt_insert->bindParam(':url_short',$shortened_url);
        $stmt_insert->bindParam(':url_full',$fullurl);
        $stmt_insert->execute();
        return json_encode(array(
            'msg'=>'success',
            'id'=>$id,
            'url_s'=>$shortened_url,
            'url_f'=>$fullurl
        ));
    }
}