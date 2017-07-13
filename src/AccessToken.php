<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\caching\Cache;
use yii\di\Instance;
use yii\base\Component;
use yii\base\InvalidConfigException;
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
            $this->cache = Instance::ensure($this->cache, Cache::class);
        }
    }

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
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = [__CLASS__, 'appId' => $this->appId];
        if ($forceRefresh || ($accessToken = $this->cache->get($cacheKey)) === false) {
            $token = $this->getTokenFromServer();
            $this->cache->set($cacheKey, $token[$this->tokenJsonKey], $token['expires_in'] - 1500);
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
        $this->cache->set([__CLASS__, 'appId' => $this->appId], $token, $expires - 1500);
        return $this;
    }

    /**
     * 从微信服务器获取Token
     * @return array|mixed
     * @throws Exception
     */
    public function getTokenFromServer()
    {
        $params = ['appid' => $this->appId, 'secret' => $this->appSecret, 'grant_type' => 'client_credential',];
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