<?php

namespace Moka;

use Moka\Random;

/**
 * Class Signature
 * @author Lucifer from Moka
 */
class Signature
{
    private static $expire = 1;  // 签名过期时间(s)

    /**
     * 生成签名
     * @param Array  $data        要签名的参数
     * @param String $sign_type   签名类型 支持 md5 sha1 sha256 haval160,4
     */
    public static function make(Array &$data ,String $sign_type ='sha1')
    {
        $data['timestamp'] = time();
        $data['nonce'] = Random::str();
        $data['sign_type'] = $sign_type;

        ksort($data);

        $data['sign'] = hash($sign_type,json_encode($data));
        unset($data['secret']);

        return $data;
    }

    /**
     * 校验签名
     */
    public static function check(Array $param)
    {
        if($param['timestamp'] + self::$expire < time()){
            return ['code'=>0,'msg'=>'签名过期'];
        }
        $sign = $param['sign'];
        unset($param['sign']);
        ksort($param);
        $calc = hash($param['sign_type'],json_encode($param));

        if($sign == $calc){
            return ['code'=>1,'msg'=>'签名通过'];
        }
        return ['code'=>0,'msg'=>'签名异常'];
    }
}
