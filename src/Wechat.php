<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\di\Instance;
use yii\base\Component;
use yii\base\InvalidConfigException;
use xutl\wechat\oauth\OAuth;
use xutl\wechat\AccessToken;
use xutl\wechat\js\Js;
use xutl\wechat\notice\Notice;
use xutl\wechat\url\Url;
use xutl\wechat\qrcode\QRCode;
use xutl\wechat\menu\Menu;
use xutl\wechat\material\Material;
use xutl\wechat\material\Temporary;
use yii\caching\CacheInterface;


/**
 * Class Wechat
 *
 * @property OAuth $oauth The oauth client collection for this wechat.
 * @property AccessToken $accessToken The access token for this wechat.
 * @property Js $js The js for this wechat.
 * @property Notice $notice The js for this wechat.
 * @property Url $url The js for this wechat.
 * @property QRCode $qrcode The js for this wechat.
 * @property Menu $menu The js for this wechat.
 * @property Material $material The js for this wechat.
 * @property Temporary $materialTemporary The js for this wechat.
 *
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
     * @var CacheInterface|array|string This can be one of the following:
     *
     * - an application component ID (e.g. `cache`)
     * - a configuration array
     * - a [[\yii\caching\Cache]] object
     *
     * When this is not set, it means caching is not enabled.
     */
    public $cache;

    /**
     * 初始化组件
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
            $this->cache = Instance::ensure($this->cache, 'yii\caching\CacheInterface');
        }
    }

    public function getAccessToken()
    {
        return Yii::createObject([
            'class' => AccessToken::className(),
            'appId' => $this->appId,
            'appSecret' => $this->appSecret,
        ]);
    }

    /**
     * check if client is wechat
     * @return bool
     */
    public function getIsWechat()
    {
        return strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false;
    }
}