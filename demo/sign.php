<?php

require __DIR__ . '/../vendor/autoload.php';

use Moka\Signature;

# client
$data = [];
$data['name'] = 'zhangsan';
$data['appid'] = '1570001110';
$data['secret'] = 'uPNkA4BlSsLnKIca';

Signature::make($data,'sha1');

p($cli=false,$data);

# server
$param = [];
# 接收的参数
$param = $data;
# 存在服务端的密钥
$param['secret'] = 'uPNkA4BlSsLnKIca';

$res = Signature::check($param);

p($cli=false,$res);

