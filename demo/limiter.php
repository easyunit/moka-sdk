<?php

require __DIR__ . '/../vendor/autoload.php';

use Moka\Limiter;


for ($i = 0; $i < 100; $i++) {
    $bool = Limiter::isActionAllowed('1', 'login');
    d($bool);
}
