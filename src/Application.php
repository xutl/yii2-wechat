<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\web\User;
use yii\helpers\Url;
use xutl\wechat\js\Js;
use xutl\wechat\oauth\OAuth;
use xutl\wechat\notice\Notice;
use xutl\wechat\qrcode\QRCode;
use xutl\wechat\menu\Menu;
use xutl\wechat\material\Material;
use xutl\wechat\material\Temporary;

/**
 * Class Application
 * @property OAuth $oauth The oauth component. This property is read-only.
 * @property AccessToken $accessToken The access token component. This property is read-only.
 * @property Js $js The access token component. This property is read-only.
 * @property Notice $notice The Notice component. This property is read-only.
 * @property Url $url The Url component. This property is read-only.
 * @property QRCode $qrcode The Url component. This property is read-only.
 * @property Menu $menu The Url component. This property is read-only.
 * @property Material $material The Url component. This property is read-only.
 * @property Temporary $materialTemporary The Url component. This property is read-only.
 * @package xutl\wechat
 */
class Application extends \yii\web\Application
{
    /**
     * Returns the oauth client collection for this application.
     * @return OAuth the oauth client collection for this application.
     */
    public function getOauth()
    {
        return $this->get('oauth');
    }

    /**
     * Returns the access token for this application.
     * @return \xutl\wechat\AccessToken the access token for this application.
     */
    public function getAccessToken()
    {
        return $this->get('accessToken');
    }

    /**
     * Returns the Js for this application.
     * @return \xutl\wechat\js\Js the Js for this application.
     */
    public function getJs()
    {
        return $this->get('js');
    }

    /**
     * Returns the Notice for this application.
     * @return \xutl\wechat\js\Notice the Js for this application.
     */
    public function getNotice()
    {
        return $this->get('notice');
    }

    /**
     * Returns the Url for this application.
     * @return Url the Js for this application.
     */
    public function getUrl()
    {
        return $this->get('url');
    }

    /**
     * Returns the QRCode for this application.
     * @return QRCode the Js for this application.
     */
    public function getQrcode()
    {
        return $this->get('qrcode');
    }

    /**
     * Returns the Menu for this application.
     * @return Menu the Js for this application.
     */
    public function getMenu()
    {
        return $this->get('menu');
    }

    /**
     * Returns the Material for this application.
     * @return Material the Js for this application.
     */
    public function getMaterial()
    {
        return $this->get('material');
    }

    /**
     * Returns the Temporary for this application.
     * @return Temporary the Js for this application.
     */
    public function getMaterialTemporary()
    {
        return $this->get('materialTemporary');
    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {

        return array_merge(parent::coreComponents(), [
//            'view' => [
//                'class' => 'xutl\wechat\View',
//            ],
            'request' => [
                'class' => 'xutl\wechat\Request',
            ],
            'response' => [
                'class' => 'xutl\wechat\Response',
            ],
            //微信专用
            'oauth' => [
                'class' => 'xutl\wechat\oauth\OAuth',
            ],
            'accessToken' => [
                'class' => 'xutl\wechat\AccessToken',
            ],
            'js' => [
                'class' => 'xutl\wechat\js\Js',
            ],
            'notice' => [
                'class' => 'xutl\wechat\notice\Notice',
            ],
            'url' => [
                'class' => 'xutl\wechat\url\url',
            ],
            'qrcode' => [
                'class' => 'xutl\wechat\qrcode\QRCode',
            ],
            'menu' => [
                'class' => 'xutl\wechat\menu\Menu',
            ],
            'material' => [
                'class' => 'xutl\wechat\material\Material',
            ],
            'materialTemporary' => [
                'class' => 'xutl\wechat\material\Temporary',
            ],
        ]);
    }
}