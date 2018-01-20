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
use xutl\wechat\Api;

/**
 * Class Js
 * @package xutl\wechat\js
 */
class Js extends Api
{

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
        $config = array_merge(['debug' => $debug, 'beta' => $beta], $signPackage, ['jsApiList' => $APIs]);
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
     * @throws \yii\httpclient\Exception
     */
    public function ticket($forceRefresh = false)
    {
        $cacheKey = [__CLASS__, 'appId' => Yii::$app->wechat->appId];
        if ($forceRefresh || ($ticket = Yii::$app->wechat->cache->get($cacheKey)) === false) {
            $response = $this->post(self::API_TICKET, ['type' => 'jsapi']);
            Yii::$app->wechat->cache->set($cacheKey, $response['ticket'], $response['expires_in'] - 500);
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
     * @throws \yii\httpclient\Exception
     */
    public function signature($url = null, $nonce = null, $timestamp = null)
    {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : uniqid();
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->ticket();
        $sign = [
            'appId' => Yii::$app->wechat->appId,
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
     * 获取当前Url,不走路由解析
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }
        return Yii::$app->request->absoluteUrl;
    }
}