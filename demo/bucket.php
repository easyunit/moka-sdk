<?php

require __DIR__ . '/../vendor/autoload.php';

use Moka\LeakyBucket;


for ($i = 0; $i < 65; $i++) {
    $res = LeakyBucket::IsPass('replay8', 60, 20, 60, 1);
    if (is_bool($res)) {
        dd('请安装redis-cell模块扩展');
    }

    if ($res[0]) {
        echo "请" . $res[3] . "s后重试哦";
    } else {
        // echo "申请令牌通过";
    }
}

// dd($res);
