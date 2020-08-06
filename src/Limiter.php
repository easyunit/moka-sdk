<?php

namespace Moka;

/**
 * Class Limiter
 * @author Lucifer from Moka
 */
class Limiter
{

    public static function isActionAllowed(Int $user_id, String $action_key, Int $period = 60, Int $max_count = 30): bool
    {
        $key = 'hist:' . $user_id . ':' . $action_key;
        $now_ts = (string) microtime(true) * 10000;

        // 通道操作
        $pipe = moka_redis()->multi(\Redis::PIPELINE);
        $pipe->zadd($key, $now_ts, $now_ts);
        $pipe->zremrangeByScore($key, 0, $now_ts - $period * 10000);
        $pipe->zcard($key);
        $pipe->expire($key, $period + 1);
        $replies = $pipe->exec();
        $pipe->close();

        // 单独获取操作数量
        // $counter = moka_redis()->zcard($key);
        return $replies[2] <= $max_count;
    }
}
