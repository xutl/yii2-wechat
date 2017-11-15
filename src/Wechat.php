<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\caching\Cache;
use yii\di\Instance;
use xutl\wechat\js\Js;
use xutl\wechat\url\Url;
use xutl\wechat\menu\Menu;
use xutl\wechat\oauth\OAuth;
use xutl\wechat\qrcode\QRCode;
use xutl\wechat\notice\Notice;
use xutl\wechat\material\Material;
use xutl\wechat\material\Temporary;

/**
 * Class Wechat
 *
 * @property OAuth $oauth
 * @property AccessToken $accessToken
 * @property Js $js
 * @property Notice $notice
 * @property Url $url
 * @property Menu $menu
 * @property QRCode $qrcode
 * @property Material $material
 * @property Temporary $materialTemporary
 * @property Cache $cache
 * @package xutl\wechat
 */
class Wechat extends Component
{
    /**
     * @var string
     */
    public $appId;

    /**
     * @var string
     */
    public $appSecret;

    /**
     * @var string|Cache
     */
    public $cache = 'cache';

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        parent::init();
        if (empty ($this->appId)) {
            throw new InvalidConfigException ('The "appId" property must be set.');
        }
        if (empty ($this->appSecret)) {
            throw new InvalidConfigException ('The "appSecret" property must be set.');
        }
        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, Cache::class);
        }
    }

    /**
     * @return object|AccessToken
     */
    public function getAccessToken()
    {
        return Yii::createObject([
            'class' => 'xutl\wechat\AccessToken',
        ]);
    }

    /**
     * @return object|OAuth
     */
    public function getOauth()
    {
        return Yii::createObject([
            'class' => 'xutl\wechat\oauth\OAuth',
        ]);
    }

    /**
     * @return object|Js
     */
    public function getJs()
    {
        return Yii::createObject([
            'class' => 'xutl\wechat\js\Js',
        ]);
    }

    /**
     * @return object|Notice
     */
    public function getNotice()
    {
        return Yii::createObject([
            'class' => 'xutl\wechat\notice\Notice',
        ]);
    }

    /**
     * @return object|Url
     */
    public function getUrl()
    {
        return Yii::createObject([
            'class' => 'xutl\wechat\url\Url',
        ]);
    }

    /**
     * @return object|Menu
     */
    public function getMenu(){
        return Yii::createObject([
            'class' => 'xutl\wechat\menu\Menu',
        ]);
    }

    /**
     * @return object|QRCode
     */
    public function getQrcode(){
        return Yii::createObject([
            'class' => 'xutl\wechat\qrcode\QRCode',
        ]);
    }

    /**
     * @return object|Material
     */
    public function getMaterial(){
        return Yii::createObject([
            'class' => 'xutl\wechat\material\Material',
        ]);
    }

    /**
     * @return object|Temporary
     */
    public function getMaterialTemporary(){
        return Yii::createObject([
            'class' => 'xutl\wechat\material\Temporary',
        ]);
    }
}