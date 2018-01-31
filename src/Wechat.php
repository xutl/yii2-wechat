<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\di\Instance;
use yii\caching\Cache;
use yii\di\ServiceLocator;
use yii\base\InvalidConfigException;
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
 * @property OAuth $oAuth 公众平台
 * @property OAuth $openOAuth 开放平台
 * @property AccessToken $accessToken
 * @property Js $js
 * @property Notice $notice
 * @property Url $url
 * @property Menu $menu
 * @property QRCode $qrcode
 * @property Material $material
 * @property Temporary $materialTemporary
 * @property Cache $cache
 *
 * @package xutl\wechat
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class Wechat extends ServiceLocator
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
     * @var array wechat parameters (name => value).
     */
    public $params = [];

    /**
     * Payment constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->preInit($config);
        parent::__construct($config);
    }

    /**
     * 预处理组件
     * @param array $config
     */
    public function preInit(&$config)
    {
        // merge core components with custom components
        foreach ($this->coreComponents() as $id => $component) {
            if (!isset($config['components'][$id])) {
                $config['components'][$id] = $component;
            } elseif (is_array($config['components'][$id]) && !isset($config['components'][$id]['class'])) {
                $config['components'][$id]['class'] = $component['class'];
            }
        }
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     * @throws InvalidConfigException
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
     * @throws InvalidConfigException
     */
    public function getAccessToken()
    {
        return $this->get('accessToken');
    }

    /**
     * @return object|OAuth
     * @throws InvalidConfigException
     */
    public function getOAuth()
    {
        return $this->get('oauth');
    }

    /**
     * @return object|OAuth
     * @throws InvalidConfigException
     */
    public function getOpenOAuth()
    {
        return $this->get('openOAuth');
    }

    /**
     * @return object|Js
     * @throws InvalidConfigException
     */
    public function getJs()
    {
        return $this->get('js');
    }

    /**
     * @return object|Notice
     * @throws InvalidConfigException
     */
    public function getNotice()
    {
        return $this->get('notice');
    }

    /**
     * @return object|Url
     * @throws InvalidConfigException
     */
    public function getUrl()
    {
        return $this->get('url');
    }

    /**
     * @return object|Menu
     * @throws InvalidConfigException
     */
    public function getMenu()
    {
        return $this->get('menu');
    }

    /**
     * @return object|QRCode
     * @throws InvalidConfigException
     */
    public function getQrcode()
    {
        return $this->get('qrcode');
    }

    /**
     * @return object|Material
     * @throws InvalidConfigException
     */
    public function getMaterial()
    {
        return $this->get('material');
    }

    /**
     * @return object|Temporary
     * @throws InvalidConfigException
     */
    public function getMaterialTemporary()
    {
        return $this->get('materialTemporary');
    }

    /**
     * Returns the configuration of wechat components.
     * @see set()
     */
    public function coreComponents()
    {
        return [
            'accessToken' => ['class' => 'xutl\wechat\AccessToken'],
            'oauth' => ['class' => 'xutl\wechat\oauth\OAuth'],
            'openOAuth' => ['class' => 'xutl\wechat\oauth\OAuth'],
            'js' => ['class' => 'xutl\wechat\js\Js'],
            'notice' => ['class' => 'xutl\wechat\notice\Notice'],
            'url' => ['class' => 'xutl\wechat\url\Url'],
            'menu' => ['class' => 'xutl\wechat\menu\Menu'],
            'qrcode' => ['class' => 'xutl\wechat\qrcode\QRCode'],
            'material' => ['class' => 'xutl\wechat\material\Material'],
            'materialTemporary' => ['class' => 'xutl\wechat\material\Temporary'],
        ];
    }
}