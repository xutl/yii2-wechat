<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use yii\httpclient\Exception;

/**
 * Class AccessToken
 * @property string $token 获取Token
 * @package xutl\wechat
 */
class AccessToken extends Component
{
    /**
     * Query name.
     *
     * @var string
     */
    public $queryName = 'access_token';

    /**
     * Response Json key name.
     *
     * @var string
     */
    public $tokenJsonKey = 'access_token';

    /**
     * @var Client
     */
    private $_httpClient;

    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * 获取Http Client
     * @return Client
     */
    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $this->_httpClient = new Client([
                'requestConfig' => [
                    'options' => [
                        'timeout' => 30,
                    ]
                ],
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON
                ],
            ]);
        }
        return $this->_httpClient;
    }

    /**
     * Get token from WeChat API.
     *
     * @param bool $forceRefresh
     *
     * @return string
     * @throws Exception
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = [__CLASS__, 'appId' => Yii::$app->wechat->appId];
        if ($forceRefresh || ($accessToken = Yii::$app->wechat->cache->get($cacheKey)) === false) {
            $token = $this->getTokenFromServer();
            Yii::$app->wechat->cache->set($cacheKey, $token[$this->tokenJsonKey], $token['expires_in'] - 1500);
            return $token[$this->tokenJsonKey];
        }
        return $accessToken;
    }

    /**
     * 设置自定义 token.
     *
     * @param string $token
     * @param int $expires
     *
     * @return $this
     */
    public function setToken($token, $expires = 7200)
    {
        Yii::$app->wechat->cache->set([__CLASS__, 'appId' => Yii::$app->wechat->appId], $token, $expires - 1500);
        return $this;
    }

    /**
     * 从微信服务器获取Token
     * @return array|mixed
     * @throws Exception
     */
    public function getTokenFromServer()
    {
        $params = ['appid' => Yii::$app->wechat->appId, 'secret' => Yii::$app->wechat->appSecret, 'grant_type' => 'client_credential',];
        $response = $this->getHttpClient()->createRequest()
            ->setUrl(self::API_TOKEN_GET)
            ->setMethod('GET')
            ->setData($params)
            ->send();

        if (!$response->isOk || empty($response->data[$this->tokenJsonKey])) {
            throw new Exception('Request AccessToken fail. response: ' . $response->content, $response->statusCode);
        }
        return $response->data;
    }
}