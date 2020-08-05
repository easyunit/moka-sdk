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

- 支持框架 thinkphp 5.0
- 支持redis单机 支持redis集群 请参考将config/redis.php移到app/extra目录下，并完成相关的redis配置


- 单例模式说明
  
  - moka_redis() 会对传入的不同的配置项，进行单例链接
  
- 集群与分布式集群说明
  - 使用分布式集群 端口和ip不同时需要将配置写入cluster的位置
  - 使用阿里云集群时，阿里云会自动管理ip和端口，咱们作为阿里云redis购买方，拿到的是单个ip和port，使用default等配置即可
  
- 函数

  ```php
  /**
   * @param $dbindex 要链接的redis库
   * @param $config  要使用的配置
   * @return 链接池
   */
  moka_redis(Int $dbindex=0,String $config='default') : connected
  ```

- 使用示例

```php
moka_redis()->get('name');   // 使用默认配置进行操作
moka_redis(0,'mid')->get('name');  // 切换中台配置进行操作
moka_redis(0,'cluster')->get('name');   // 使用分布式集群进行操作
```

