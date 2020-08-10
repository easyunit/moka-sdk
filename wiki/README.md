[TOC]



## 安装

```bash
composer require easyunit/moka-sdk=1.0.0.x-dev
```

## 类库使用示例

### AES对称机密

- 客户端说明
  - key 一般为客户端和服务端之间的密钥
  - flag 标识符 可以为微信appid 可以为mokaid
  - 客户端应该加加密后的密文和flag一起发送给服务端

```php
use Moka\Aes;

$key = 'f09ae799e02a4c50ba8359153aa40efd';
$flag = '15712345678';

// 要加密的数据
$data = ['name' => 'zhangsan'];

// 加密
$encrypted = Aes::encrypt($data, $key, $flag);

// 解密
$plaintext = Aes::decrypt($encrypted, $key, $flag);
```

- 服务端说明
```php
use Moka\Aes;

$key = 'f09ae799e02a4c50ba8359153aa40efd';
$flag = '15712345678';

// 要加密的数据
$data = ['name' => 'zhangsan'];

// 加密
$encrypted = Aes::encrypt($data, $key, $flag);

// 解密
$plaintext = Aes::decrypt($encrypted, $key, $flag);
```

### 签名验证

- 客户端签名步骤
  - 一、将参数和密钥放进数组
  - 二、数组内添加随机字符串和时间戳(在make()内部实现)
  - 三、对数组的key进行排序
  - 四、选择签名方式进行签名，支持md5,sha1,sha256
  - 五、将签名后的数据发送

```php
use Moka\Signature;

// 要发送给服务端的数据
$data = [];
$data['name'] = 'zhangsan';
$data['appid'] = '1570001110';

// 用于制作签名
$data['secret'] = 'uPNkA4BlSsLnKIca';

Signature::make($data,'sha1');   // 得到新$data会移除$data['secret'] 会添加$data['sign']
dd($data);
```

- 服务端验签步骤
  - 一、服务端不会收到客户端发来的密钥，需要在服务端获取、并放进数组
  - 二、验证签名时间戳是否过期
  - 二、将数组的sign弹出并用$sign接收
  - 三、对数组的key进行排序
  - 四、对数组进行签名和$sign进行对比

```php
use Moka\Signature;

$param = request()->param();  // 接收客户端传递的参数

// 将密钥赋值给数组
$param['secret'] = 'uPNkA4BlSsLnKIca';

// 验证签名
$res = Signature::check($param);
```



## Redis链接封装

- 支持redis单机 支持redis集群 tp框架请将config/redis.php移到app/extra目录下，并完成相关的redis配置


- 单例模式说明
  
  - moka_redis() 会对传入的不同的配置项，进行单例链接
  
- 集群与分布式集群说明
  - 使用分布式集群 端口和ip不同时需要将配置写入cluster的位置
  - 使用阿里云集群时，阿里云会自动管理ip和端口，咱们作为阿里云redis购买方，拿到的是单个ip和port，使用default等配置即可
  
- 助手函数


  - 用户需要此函数放在公共助手函数中

  ```php
  /**
   * @param $dbindex 要链接的redis库
   * @param $config  要使用的配置
   * @return 链接池
   */
  function moka_redis(Int $dbindex = 0, $config = 'default')
  {
      $conf = \think\Config::get('redis.' . $config);   // 此处为tp框架配置  // 如果需要使用laravel框架 需自行读取配置
    	$conf['flag'] = $config;
      return \Moka\Redis::instance($dbindex, $conf);
  }
  ```

- 使用示例

```php
moka_redis()->get('name');   // 使用默认配置进行操作
moka_redis(0,'mid')->get('name');  // 切换中台配置进行操作
moka_redis(0,'cluster')->get('name');   // 使用分布式集群进行操作
```

## 接口限流

- 计数器限流

  - 如果$period和$max_count允许的数量都很多，遇到并发操作时，redis占用内存会比较高
  - redis默认配置为127.0.0.1 6379，composer安装到tp框架时，链接不同的数据库参考Redis链接封装

  ```php
  /**
   * @param $user_id 用户id
   * @param $action  要进行的操作 比如 reply 回复帖子
   * @param $period  计数单位 默认60秒
   * @param $max_count 允许的操作,[60s]内允许操作30次
   * @return bool 是否允许操作
   */
  Limiter::isActionAllowed(Int $user_id,$action,$period=60,$max_count=30) : bool
  ```

  - 使用示例

  ```php
  use Moka\Limiter;
  $bool = Limiter::isActionAllowed(1,'repay',60,30);
  if($bool){
    echo '操作成功';
  }else{
    echo '请勿频繁操作';
  }
  ```

- 漏斗限流

  - 需要安装redis-cell扩展

  - 需要自行实现moka_redis()

    ```php
        /**
         * 限流
         * @param String $action    要进行的操作 比如 reply:1 用户1进行回复
         * @param Int    $max_burst 漏斗容量||初始令牌数      比如用户1初始情况下，可以连续回复15次
         * @param Int    $max_count 消化速率||时间内产生令牌的数量
         * @param Int    $period    时间限制
         * @param Int    $apply     默认申请令牌数量
         * @return Array
         */
    LeakyBucket::IsPass($action, $max_burst = 15, $max_count = 30, $period = 60, $apply = 1)
    ```

  - 返回值说明

    - 类型 返回bool说明未安装redis-cell插件，返回数组，说明操作成功，需用户自行判断是否允许通过
  
    ```json
    [
    	'0' => 0,		// 0 请求通过，1 请求拒绝
    	'1' => 16,	// 容量
    	'2' => 15,	// 剩余空间
    	'3' => -1,	// 如果请求被拒绝，请多少s后重试
    	'4' => 15   // 多长时间后，漏斗完全空闲
    ]
    ```
  
  

## 接口监控

- 统计接口访问次数(PV)

  - 示例 在需要统计的地方写一行```Monitor::Pv($url, 8);```

  ```php
  require __DIR__ . '/../vendor/autoload.php';
  
  $url = $_SERVER['PATH_INFO'] ?? '/';
  
  Monitor::Pv($url, 8);  // 日 小时 分钟 秒 pv
  Monitor::Pv($url, 4);  // 小时 分钟 秒 pv
  Monitor::Pv($url, 2);  // 分钟 秒 pv
  Monitor::Pv($url, 1);  // 秒 pv
  ```

  - 参数说明
    - $url     要统计的接口或者web url
    - $level  统计级别 数字越大 统计的越全面 默认为8

- 读取统计的PV 比如在管理后台读取之后绘制曲线图

  - 示例

  ```php
  require __DIR__ . '/../vendor/autoload.php';
  $data = Monitor::GetPv($url, null, 8);  // 读7天数据
  $data = Monitor::GetPv($url, 3, 8);			// 读3天数据
  $data = Monitor::GetPv($url, 8, 4);			// 读8天小时数据
  ```

  - 参数说明
    - $url         要读取的接口或者web url的pv，默认为null，读取所有
    - $section 要读取的区间 默认7天 12小时 30分钟 60秒
    - $level      读取级别，8=读取天PV，4=读取小时PV，2=读取分钟PV，1=读取秒PV

- 删除期限外PV

  ```php
  require __DIR__ . '/../vendor/autoload.php';
  Monitor::DelPv(null, null, 2);
  ```

  - 参数说明
    - $url          要删除的url
    - $section 要删除的区间 默认(7,14)天，（12,24]小时，(30,60]分钟,(60,150]秒
    - $level      要删除的项
      - 8 删除日PV     建议每日执行
      - 4 删除小时PV 建议每小时执行
      - 2 删除分钟PV 建议每15分钟执行
      - 1 删除秒PV 建议每分钟执行

- 清除所有PV

  - 这里清除的是所有PV统计，建议用户根据自己的业务针对清除

  ```php
  require __DIR__ . '/../vendor/autoload.php';
  Monitor::ClearPv();
  ```