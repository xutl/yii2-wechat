<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\js;

use Yii;
use yii\di\Instance;
use yii\helpers\Json;
use yii\caching\Cache;
use yii\helpers\Url;
use xutl\wechat\BaseApi;

/**
 * Class Js.
 */
class Js extends BaseApi
{
    /**
     * Cache.
     *
     * @var Cache
     */
    public $cache = 'cache';

    /**
     * Current URI.
     *
     * @var string
     */
    protected $url;

    /**
     * Api of ticket.
     */
    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';

    /**
     * 初始化组件
     */
    public function init()
    {
        parent::init();
        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, 'yii\caching\CacheInterface');
        }
    }

    /**
     * Get config json for jsapi.
     *
     * @param array $APIs
     * @param bool $debug
     * @param bool $beta
     * @param bool $json
     *
     * @return array|string
     */
    public function config(array $APIs, $debug = false, $beta = false, $json = true)
    {
        $signPackage = $this->signature();
        $base = ['debug' => $debug, 'beta' => $beta,];
        $config = array_merge($base, $signPackage, ['jsApiList' => $APIs]);
        return $json ? Json::encode($config) : $config;
    }

    /**
     * Return jsapi config as a PHP array.
     *
     * @param array $APIs
     * @param bool $debug
     * @param bool $beta
     *
     * @return array
     */
    public function getConfigArray(array $APIs, $debug = false, $beta = false)
    {
        return $this->config($APIs, $debug, $beta, false);
    }

    /**
     * Get jsticket.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function ticket($forceRefresh = false)
    {
        $cacheKey = [__CLASS__, 'appId' => Yii::$app->accessToken->appId];
        if ($forceRefresh || ($ticket = $this->cache->get($cacheKey)) === false) {
            $response = $this->sendRequest(self::POST, self::API_TICKET, ['type' => 'jsapi']);
            $this->cache->set($cacheKey, $response['ticket'], $response['expires_in'] - 500);
            return $response['ticket'];
        }
        return $ticket;
    }

    /**
     * Build signature.
     *
     * @param string $url
     * @param string $nonce
     * @param int $timestamp
     *
     * @return array
     */
    public function signature($url = null, $nonce = null, $timestamp = null)
    {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : uniqid();
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->ticket();
        $sign = [
            'appId' => Yii::$app->accessToken->appId,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
        ];

        return $sign;
    }

    /**
     * 签名参数
     *
     * @param string $ticket
     * @param string $nonce
     * @param int $timestamp
     * @param string $url
     *
     * @return string
     */
    public function getSignature($ticket, $nonce, $timestamp, $url)
    {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    /**
     * Set current url.
     *
     * @param string $url
     *
     * @return Js
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get current url.
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }
        return Url::current();
    }
}