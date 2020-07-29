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
