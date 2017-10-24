<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\base\Widget;
use yii\web\View;

/**
 * Class JsWidget
 * @package xutl\wechat
 */
class JsWidget extends Widget
{
    /**
     * @var array 使用的API列表
     */
    public $apiList = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        $this->initOptions();
        $this->registerAssets();
    }

    /**
     * Initializes the widget options
     */
    protected function initOptions()
    {
        $this->apiList = array_merge([
            //基础接口
            'checkJsApi',//判断当前客户端是否支持指定JS接口

            //分享接口
            'onMenuShareTimeline',//获取分享到朋友圈按钮点击状态及自定义分享内容
            'onMenuShareAppMessage',//获取“分享给朋友”按钮点击状态及自定义分享内容接口
            'onMenuShareQQ',//获取“分享到QQ”按钮点击状态及自定义分享内容接口
            'onMenuShareWeibo',//获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口
            'onMenuShareQZone',//获取“分享到QZone”按钮点击状态及自定义分享内容接口

            //智能接口
            'translateVoice',//识别音频并返回识别结果接口

            //音频接口
            'startRecord',//开始录音接口
            'stopRecord',//停止录音接口
            'onVoiceRecordEnd',
            'playVoice',//播放语音接口
            'onVoicePlayEnd',
            'pauseVoice',//暂停播放接口
            'stopVoice',//停止播放接口
            'uploadVoice',//上传语音接口
            'downloadVoice',//下载语音接口

            //图像接口
            'chooseImage',//拍照或从手机相册中选图接口
            'previewImage',//预览图片接口
            'uploadImage',//上传图片接口
            'downloadImage',//下载图片接口

            //设备信息接口
            'getNetworkType',//获取网络状态接口

            //地理位置接口
            'openLocation',//使用微信内置地图查看位置接口
            'getLocation', //获取地理位置接口

            //界面操作接口
            'hideAllNonBaseMenuItem',//隐藏所有非基础按钮接口
            'showAllNonBaseMenuItem',//显示所有功能按钮接口
            'hideMenuItems',//隐藏右上角菜单接口
            'showMenuItems',//显示右上角菜单接口
            'hideOptionMenu',//批量隐藏功能按钮接口
            'showOptionMenu',//批量显示功能按钮接口
            'closeWindow', //关闭当前网页窗口接口

            //微信扫一扫
            'scanQRCode',//调起微信扫一扫接口

            //微信支付接口
            'chooseWXPay',//发起一个微信支付请求

            //微信小店接口
            'openProductSpecificView',//跳转微信商品页接口
            //微信卡券接口
            'addCard',//批量添加卡券接口
            'chooseCard',//调起适用于门店的卡券列表并获取用户选择列表
            'openCard'//查看微信卡包中的卡券接口
        ], $this->apiList);
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $config = Yii::$app->wechat->js->config($this->apiList);
        $view = $this->getView();
        WechatAsset::register($view);
        $view->registerJs("wx.config({$config});wx.error(function(res){console.error(res.errMsg);});wx.ready(function (){console.info('wechat jssdk init.');});", View::POS_END);
    }
}