<?php

require __DIR__ . '/../vendor/autoload.php';

use Moka\Funnel;

$funnels = [];
global $funnel;

for ($i = 0; $i < 30; $i++) {
    echo $i;
    // user_id 操作 最大容量 流水速率
    d(isActionAllowed("110", "reply", 15, 0.3));
    echo '</br>';
    sleep(1);
}
