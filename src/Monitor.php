<?php

namespace Moka;

use phpDocumentor\Reflection\Types\Integer;

/**
 * Class Monitor
 * @author Lucifer from Moka
 */
class Monitor
{

    /**
     * 设置Pv
     * @param Sting $url   访问的url
     * @param Int   $level 记录的等级，1记录秒 2记录分钟 4记录小时 8记录天
     */
    public static function Pv(String $url, Int $level = 8): bool
    {
        date_default_timezone_set('Asia/Shanghai');
        $now = time();
        moka_redis()->hset('pv_list', $url, $url); // 方便定时器定期清理PV
        switch ($level) {
            case '8':
                $total_key = 'pv_day:all';
                $key = 'pv_day:' . $url;
                $today = strtotime(date('Ymd', $now));
                moka_redis()->zincrby($total_key, 1, $today);
                moka_redis()->zincrby($key, 1, $today);
            case '4':
                $total_key = 'pv_hour:all';
                $key = 'pv_hour:' . $url;
                $hour_str = date('Y-m-d H', $now);
                $hour = strtotime($hour_str . ':00:00');
                moka_redis()->zincrby($total_key, 1, $hour);
                moka_redis()->zincrby($key, 1, $hour);
            case '2':
                $total_key = 'pv_min:all';
                $key = 'pv_min:' . $url;
                $min = strtotime(date('YmdHi', $now));
                moka_redis()->zincrby($total_key, 1, $min);
                moka_redis()->zincrby($key, 1, $min);
            case '1':
            default:
                $total_key = 'pv_sec:all';
                $key = 'pv_sec:' . $url;
                moka_redis()->zincrby($total_key, 1, $now);
                moka_redis()->zincrby($key, 1, $now);
                break;
        }
        return true;
    }

    /**
     * 获取pv
     * @param Sting $url        访问的url 默认all
     * @param Int   $section    扫描区间 默认扫描 60s 30m 12h 7d
     * @param Int   $level      扫描等级 1按秒扫描 2按分钟扫描 4按小时扫描 8按天扫描
     */
    public static function GetPv($url = null, Int $section = null, Int $level = 8): array
    {
        date_default_timezone_set('Asia/Shanghai');
        $now = time();

        switch ($level) {
            case '8':
                $key = $url ? 'pv_day:' . $url : 'pv_day:all';
                $section = $section ?? 7;
                $today = strtotime(date('Ymd', $now));
                $member = array();
                for ($i = 0; $i < $section; $i++) {
                    $member[$i] = $today - 86400 * $i;
                }
                break;
            case '4':
                $key = $url ? 'pv_hour:' . $url : 'pv_hour:all';
                $section = $section ?? 12;
                $hour_str = date('Y-m-d H', $now);
                $hour = strtotime($hour_str . ':00:00');
                $member = array();
                for ($i = 0; $i < $section; $i++) {
                    $member[$i] = $hour - 3600 * $i;
                }
                break;
            case '2':
                $key = $url ? 'pv_min:' . $url : 'pv_min:all';
                $section = $section ?? 30;

                $min = strtotime(date('Y-m-d H:i', $now));
                $member = array();
                for ($i = 0; $i < $section; $i++) {
                    $member[$i] = $min - 60 * $i;
                }
                break;
            case '1':
            default:
                $key = $url ? 'pv_sec:' . $url : 'pv_sec:all';
                $section = $section ?? 60;
                $member = array();
                for ($i = 0; $i < $section; $i++) {
                    $member[$i] = $now - $i;
                }

                break;
        }
        $data = array();
        foreach ($member as $k => $value) {
            $data[$value] = (int) moka_redis()->zscore($key, $value);
        }
        ksort($data);
        return $data;
    }

    /**
     * 删除PV
     * @param Sting $url        访问的url 默认all
     * @param Int   $section    删除区间 默认删除 60s 30m 12h 7d 外
     * @param Int   $level      删除登记 1删除秒PV 2删除分钟PV 4删除小时PV 8删除天PV
     */
    public static function DelPv(String $url = null, Int $section = null, Int $level = 8): bool
    {
        date_default_timezone_set('Asia/Shanghai');
        $now = time();

        switch ($level) {
            case '8': // 每天执行一次 删除(7,14]天
                $key = $url ? 'pv_day:' . $url : 'pv_day:all';
                $section = $section ?? 7;
                $section_x2 =  $section * 2;
                $today = strtotime(date('Ymd', $now));
                $member = array();
                // for ($i = 0; $i < $section; $i++) {
                //     $member[$i] = $today - 86400 * $i;
                // }
                for ($i = $section; $i < $section_x2; $i++) {
                    $mem = $member[$i] = $today - 86400 * $i;
                    moka_redis()->zrem($key, $mem);
                }
                break;
            case '4': // 每小时执行一次 删除(12,24]小时
                $key = $url ? 'pv_hour:' . $url : 'pv_hour:all';
                $section = $section ?? 12;
                $section_x2 =  $section * 2;
                $hour_str = date('Y-m-d H', $now);
                $hour = strtotime($hour_str . ':00:00');
                $member = array();
                for ($i = $section; $i < $section_x2; $i++) {
                    $mem = $member[$i] = $hour - 3600 * $i;
                    moka_redis()->zrem($key, $mem);
                }
                break;
            case '2': // 每15分钟执行一次 删除(30-60]分钟
                $key = $url ? 'pv_min:' . $url : 'pv_min:all';
                $section = $section ?? 30;
                $section_x2 =  $section * 2;
                $min = strtotime(date('Y-m-d H:i', $now));
                $member = array();
                for ($i = $section; $i < $section_x2; $i++) {
                    $mem = $member[$i] = $min - 60 * $i;
                    moka_redis()->zrem($key, $mem);
                }
                break;
            case '1':
            default: // 每分钟执行一次 删除(60,150]秒
                $key = $url ? 'pv_sec:' . $url : 'pv_sec:all';
                $section = $section ?? 60;
                $section_x2 =  $section * 2.5;
                $member = array();
                for ($i = 0; $i < $section; $i++) {
                    $mem = $member[$i] = $now - $i;
                    moka_redis()->zrem($key, $mem);
                }
                break;
        }

        // 删除多个
        // $res = moka_redis()->zrem($key, 1596854700, 1596854760);

        return true;
    }

    /**
     * 清除所有PV
     */
    public static function ClearPv()
    {
        $res = moka_redis()->HGetAll('pv_list');
        moka_redis()->del('pv_sec:all');
        moka_redis()->del('pv_min:all');
        moka_redis()->del('pv_hour:all');
        moka_redis()->del('pv_day:all');

        foreach ($res as $key => $value) {
            moka_redis()->del('pv_sec:' . $value);
            moka_redis()->del('pv_min:' . $value);
            moka_redis()->del('pv_hour:' . $value);
            moka_redis()->del('pv_day:' . $value);
        }
        moka_redis()->del('pv_list');
    }
}
