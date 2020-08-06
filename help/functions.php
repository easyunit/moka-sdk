<?php

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

if (!function_exists('moka_redis')) {
    /**
     * -------------------------------------------
     * Redis连接对象
     * @param Integer $dbindex 数据库
     * @param String   $config  配置项文件名
     * -------------------------------------------
     */
    function moka_redis(Int $dbindex = 0, String $config = 'default')
    {
        // 使用tp5时，请注释掉 if 内内容 此作为测试使用
        if ($config = 'default') {
            $config = array();
            $config['host'] = '127.0.0.1';
            $config['port'] = 6379;
            $config['pass'] = 'root';
        }
        return \Moka\Redis::instance($dbindex, $config);
    }
}
