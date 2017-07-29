<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\base\Widget;

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
        if (!Yii::$app instanceof \xutl\wechat\Application) {

        }
        $this->apiList = array_merge(['checkJsApi'], $this->apiList);
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $config = Yii::$app->js->config($this->apiList);
        $view = $this->getView();
        WechatAsset::register($view);
        $view->registerJs("wx.config({$config});wx.error(function(res){console.log(res);});", View::POS_END);
    }
}