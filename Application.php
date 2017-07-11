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
use yii\web\Session;
use yii\web\ErrorHandler;
use yii\httpclient\Client;
use yii\authclient\Collection;
use yii\web\NotFoundHttpException;
use yii\base\InvalidRouteException;
use yii\web\UrlNormalizerRedirectException;

/**
 * Class Application
 * @property Collection $authClientCollection The auth client component. This property is read-only.
 * @package xutl\wechat
 */
class Application extends \yii\web\Application
{
    /**
     * @var bool 是否使用企业号
     */
    public $useQy = false;

    /**
     * Returns the error handler component.
     * @return ErrorHandler the error handler application component.
     */
    public function getErrorHandler()
    {
        return $this->get('errorHandler');
    }

    /**
     * Returns the authClient component.
     * @return Collection the authClient application component.
     */
    public function getAuthClientCollection()
    {
        return $this->get('authClientCollection');
    }

    /**
     * Returns the request component.
     * @return Request the request component.
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Returns the response component.
     * @return Response the response component.
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * Returns the session component.
     * @return Session the session component.
     */
    public function getSession()
    {
        return $this->get('session');
    }

    /**
     * Returns the user component.
     * @return User the user component.
     */
    public function getUser()
    {
        return $this->get('user');
    }

    public function getJsApiTicket()
    {
        $cache = $this->cache; // Could be Yii::$app->cache
        return $cache->getOrSet(__METHOD__, function ($cache) {
            $client = new Client();
            $client->get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret");
            return Products::find()->mostPopular()->limit(10)->all();
        }, 7000);
    }

    public function getAccessToken()
    {
        $cache = $this->cache; // Could be Yii::$app->cache
        return $cache->getOrSet(__METHOD__, function ($cache) {
            $client = new Client();
            $client->get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret");
            return Products::find()->mostPopular()->limit(10)->all();
        }, 7000);
    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'view' => [
                'class' => 'xutl\wechat\View',
            ],
            'request' => [
                'class' => 'xutl\wechat\Request',
                'parsers' => [
                    'application/json' => 'yii\web\JsonParser',
                    'text/json' => 'yii\web\JsonParser',
                ],
            ],
            'response' => ['class' => 'yii\wechat\Response'],
            'session' => ['class' => 'yii\web\Session'],
            'user' => ['class' => 'yii\web\User'],
            'errorHandler' => ['class' => 'yii\web\ErrorHandler'],
            'authClientCollection' => ['class' => 'yii\authclient\Collection'],
        ]);
    }
}