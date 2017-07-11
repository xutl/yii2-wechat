<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\wechat;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the [jQuery](http://jquery.com/) JavaScript library.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class WechatAsset extends AssetBundle
{

    public $js = [
        '//res.wx.qq.com/open/js/jweixin-1.2.0.js',
    ];
}