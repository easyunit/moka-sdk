<?php

require __DIR__ . '/../vendor/autoload.php';

use Moka\Monitor;


$url = $_SERVER['PATH_INFO'] ?? '/';

// 批量设置Pv
for ($i = 0; $i < 1000; $i++) {
    $res = Monitor::Pv($url, 8);  // 模拟设置 日 小时 分钟 秒 pv
    // $res = Monitor::Pv($url, 4);  // 模拟设置 小时 分钟 秒 pv
    // $res = Monitor::Pv($url, 2);  // 模拟设置 分钟 秒 pv
    // $res = Monitor::Pv($url, 1); //  模拟设置 秒 pv
    // usleep(3000);
}

// 读取PV 绘制曲线图
$res = Monitor::GetPv($url, null, 8);  // 读取7天内所有pv
$res = Monitor::GetPv($url, null, 4);  // 读取12小时内所有pv
$res = Monitor::GetPv($url, null, 2);  // 读取30分钟内所有pv
$res = Monitor::GetPv($url, null, 1);  // 读取60秒内所有pv
// dd($res);

// 删除PV
$bool = Monitor::DelPv(null, null, 2);

// 清除所有PV
// Monitor::ClearPv();
