<?php

// require __DIR__ . '/../vendor/autoload.php';


if (!function_exists('dd')) {
    function dd($param)
    {
        header("Content-type:text/json");
        header("HTTP/1.1 400 Bad Request");
        $data['code']       = 200;
        $data['time']       = time();
        $data['url']        = $_SERVER['HTTP_HOST'] ?? '';
        $data['data']        = $param;
        echo json_encode($data);
        die;
    }
}

if (!function_exists('dd')) {
    /**
     * -------------------------------------------
     * 断点调试函数
     * -------------------------------------------
     */
    function dd()
    {
        call_user_func_array('var_dump', func_get_args());
        die();
    }
}

if (!function_exists('d')) {
    /**
     * -------------------------------------------
     * 断点调试函数
     * -------------------------------------------
     */
    function d()
    {
        call_user_func_array('var_dump', func_get_args());
    }
}

if (!function_exists('pd')) {
    /**
     * -------------------------------------------
     * 断点调试函数
     * -------------------------------------------
     */
    function pd($cli = false)
    {
        $arr = func_get_args();
        foreach ($arr as $_arr) {
            if ($cli) {
                print_r($_arr);
            } else {
                echo "<pre>";
                print_r($_arr);
                echo "</pre>";
            }
        }
        die(1);
    }
}

if (!function_exists('p')) {
    /**
     * print_r的调试输出
     */
    function p($cli = false)
    {
        $arr = func_get_args();
        foreach ($arr as $_arr) {
            if ($cli) {
                print_r($_arr);
            } else {
                echo "<pre>";
                print_r($_arr);
                echo "</pre>";
            }
        }
    }
}

// if (!function_exists('moka_redis')) {
//     /**
//      * -------------------------------------------
//      * Redis连接对象 用户需要结合框架自行实现
//      * @param Integer $dbindex 数据库
//      * @param String||Array   $config  配置项文件名
//      * -------------------------------------------
//      */
//     function moka_redis(Int $dbindex = 0, $config = 'default')
//     {

//         if ($config = 'default') {
//             // 此处为测试时使用
//             $conf = array();
//             $conf['host'] = '127.0.0.1';
//             $conf['port'] = 16379;
//             $conf['pass'] = 'root';
//             $conf['flag'] = 'default';
//         } else {
//             // 此处需要用户自行实现
//             $conf = \think\Config::get('redis.' . $config);
//             $conf['flag'] = $config;
//         }


//         return \Moka\Redis::instance($dbindex, $conf);
//     }
// }
