<?php
namespace App\Utils;

class UrlTrans
{
    public static function IDtoURL($id)
    {
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $ret = '';
        do {
            $ret = $dict[bcmod($id, 62)] . $ret; //bcmod取得高精确度数字的余数。
            $id = bcdiv($id, 62);  //bcdiv将二个高精确度数字相除。


        } while ($id > 0);
        return $ret;
    }
    public static function URLtoID($url)
    {
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $url = strval($url);
        $len = strlen($url);
        $dec = 0;
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos($dict, $url[$i]);
            $dec = bcadd(bcmul(bcpow(62, $len - $i - 1), $pos), $dec);
        }
        return (int)$dec;
    }
}