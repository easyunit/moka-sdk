<?php

namespace Moka;

/**
 * Class Signature
 * @author Lucifer from Moka
 */
class Random{

    public static function str(Int $length = 16, String $pool = ''){
        if (empty($pool)) {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        return mb_substr(str_shuffle(str_repeat($pool, $length)), 0, $length, 'UTF-8');
    }
}
