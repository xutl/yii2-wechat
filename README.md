# yii2-wechat

这是一个适用于Yii2的微信SDK

## Feature

 - 命名不那么乱七八糟；
 - 隐藏开发者不需要关注的细节；
 - 方法使用更优雅，不必再去研究那些奇怪的的方法名或者类名是做啥用的；
 - 符合 [PSR](https://github.com/php-fig/fig-standards) 标准，你可以各种方便的与你的框架集成；
 - 高度抽象的消息类，免去各种拼json与xml的痛苦；
 - 详细 Debug 日志，一切交互都一目了然；

## Requirement

1. PHP >= 5.5.9
2. **[composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

## Installation

```shell
composer require "xutl/yii2-wechat:~1.0" -vvv
```

## Usage

基本使用（以服务端为例）:

```php
<?php


```


## Documentation

- 微信公众平台文档: https://mp.weixin.qq.com/wiki

## Thanks to

* [Yii framework](https://github.com/yiisoft/yii2)
* [easywechat](https://github.com/overtrue/wechat)。

## License

MIT
