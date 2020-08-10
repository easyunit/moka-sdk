<?php

namespace Moka;

/**
 * LeakyBucket
 * @author Lucifer from Moka
 */
class LeakyBucket
{


    /**
     * 请求是否通过
     * @param String $action    要进行的操作
     * @param Int    $max_burst 初始令牌数
     * @param Int    $max_count 时间内产生令牌的数量
     * @param Int    $period    时间限制
     * @param Int    $apply     默认申请令牌数量
     * @return
     */
    public static function IsPass($action, $max_burst = 15, $max_count = 30, $period = 60, $apply = 1)
    {
        return $res = moka_redis()->rawCommand('cl.throttle', $action, $max_burst, $max_count, $period, $apply);
    }
}
