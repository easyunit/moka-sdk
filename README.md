[TOC]



## 安装

```bash
composer require easyunit/moka-sdk=1.0.0.x-dev
```

## 类库使用示例

### AES加密解密库

```php
// 引用
use Moka\Aes;

// 定义 key 和 flag
$key = 'f09ae799e02a4c50ba8359153aa40efd';
$flag = '15712345678';

// 要加密的数据
$data = ['name' => 'zhangsan'];

// 加密
$encrypted = Aes::encrypt($data, $key, $flag);

// 解密
$plaintext = Aes::decrypt($encrypted, $key, $flag);
```



