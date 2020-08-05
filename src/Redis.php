<?php

namespace Moka;

/**
 * @package Redis
 * 
 */
class Redis
{
    public            $client       = null;     // 连接对象

    protected static  $_instance    = null;     // 对每个配置进行单例模式

    protected         $prefix       = null;     // Redis配置
    protected         $is_serialize = null;

    protected         $model        = null;     // 业务相关模型
    protected         $list         = null;     // 链式操作 临时存储数据

    /**
     * 单例
     */
    private function __construct(String $config,Array $conf)
    {
        if($config =='cluster'){
            // 待测试
            $this->client = new \RedisCluster('', $conf['list'], $conf['timeout'], $conf['read_timeout'], $conf['presistent'], $conf['pass']);
        }else{
            // 已测试
            $this->client = new \Redis();
            
            try {
                $this->client->connect($conf['host'], $conf['port']);
            } catch (\Throwable $th) {
                error('请检查ip和端口');
            }
   
            if (empty($conf['pass'])) {
                return error('redis密码不能为空', '', 500);
            } else {
                try {
                    $this->client->auth($conf['pass']);
                } catch (\Exception $e) {
                    return error('redis密码错误', '', 500);
                }
            }

        }

        return $this;
    }

    /**
     * 连接的实例
     * @param String||Array $config 配置项 String时配置文件名 Array配置项
     */
    public static function instance(Int $dbindex = 0,String $config = null)
    {

        if (self::$_instance[$config] === null) {
            $conf = \think\Config::get('redis.' . $config);
            self::$_instance[$config] = new self($config,$conf);
        }

        try {
            self::$_instance[$config]->client->select($dbindex);
        } catch (\Exception $e) {
            return error('选择数据库失败，请检查密码', '', 500);
        }
        return self::$_instance[$config]->client;
    }
    public function __destruct()
    {
    }
}
