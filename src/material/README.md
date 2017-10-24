# material
微信 SDK 素材管理模块

### 获取实例

```php
<?php
use Yii;

// 永久素材
$material = Yii::$app->wechat->material;
// 临时素材
$temporary = Yii::$app->wechat->materialYemporary;

```

### 永久素材 API：

上传图片:

注意：微信图片上传服务有敏感检测系统，图片内容如果含有敏感内容，如色情，商品推广，虚假信息等，上传可能失败。

```php
$result = $material->uploadImage("/path/to/your/image.jpg");  // 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
var_dump($result);
// {
//    "media_id":MEDIA_ID,
//    "url":URL
// }
```

url 只有上传图片素材有返回值。