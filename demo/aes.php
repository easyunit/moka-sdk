<?php

require __DIR__ . '/../vendor/autoload.php';

use Moka\Aes;

$key = 'f09ae799e02a4c50ba8359153aa40efd';
$flag = '15712345678';
$data = ['name' => 'zhangsan'];

// 加密数据
$encrypted = Aes::encrypt($data, $key, $flag);

// 解密数据
$plaintext = Aes::decrypt($encrypted, $key, $flag);

// 打印结果
pd($cli = false, $encrypted, $plaintext);
