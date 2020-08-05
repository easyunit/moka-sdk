<?php

/**
 * Redis配置
 * @author Lucifer <api_php@163.com>
 */
use think\Env;

return [
    // 默认业务
    'default' => [
        'host'      => Env::get('redis_default.host', '127.0.0.1'),
        'port'      => Env::get('redis_default.port', 6379),
        'pass'      => Env::get('redis_default.pass', ''),
        'dbindex'   => Env::get('redis_default.dbindex', 0),
    ],

    // 中台业务
    // 使用示例 redis(0,'mid')->get('name');
    'mid' => [ 
        'host'      => Env::get('redis_mid.host', '127.0.0.1'),
        'port'      => Env::get('mid_admin.port', 6379),
        'pass'      => Env::get('mid_admin.pass', ''),
        'dbindex'   => Env::get('mid_admin.dbindex', 0),
    ],

    // 其他业务 请自行扩展配置

    // 分布式集群
    'cluster' =>[   
        'list' => [  // 在这里配置你集群所有的ip和端口
            'tcp://127.0.0.1:26380',
            'tcp://127.0.0.1:26381',
            'tcp://127.0.0.1:26382',
            'tcp://127.0.0.1:26383',
            'tcp://127.0.0.1:26384',
            'tcp://127.0.0.1:26385',
        ],
        'pass'          => Env::get('cluster.pass', 0),
        'select'        => 0,
        'timeout'       => 30,
        'read_timeout'  => 30,
        'expire'        => 0,
        'presistent'    => true,
        'prefix'        => ''
    ]
];
