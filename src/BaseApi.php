<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\base\Component;
use yii\di\Instance;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\base\InvalidConfigException;

/**
 * Class BaseApi
 * @package xutl\api
 */
abstract class BaseApi extends Component
{
    /**
     * @var Client
     */
    public $_httpClient;

    /**
     * The request token.
     *
     * @var \xutl\wechat\AccessToken
     */
    protected $accessToken = 'accessToken';

    const GET = 'GET';
    const POST = 'POST';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->accessToken !== null) {
            $this->accessToken = Instance::ensure($this->accessToken, 'xutl\wechat\AccessToken');
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
     * Sends HTTP request.
     * @param string $method request type.
     * @param string $url request URL.
     * @param array $params request params.
     * @param array $headers additional request headers.
     * @return array response.
     * @throws Exception on failure.
     */
    protected function sendRequest($method, $url, array $params = [], array $headers = [])
    {
        $params = array_merge($params, [$this->accessToken->queryName => $this->accessToken->getToken()]);
        $response = $this->getHttpClient()->createRequest()
            ->setUrl($url)
            ->setMethod($method)
            ->setHeaders($headers)
            ->setData($params)
            ->send();
        if (!$response->isOk) {
            throw new Exception('Request fail. response: ' . $response->content, $response->statusCode);
        }
        return $response->data;
    }

    /**
     * 合并基础URL和参数
     * @param string $url base URL.
     * @param array $params GET params.
     * @return string composed URL.
     */
    protected function composeUrl($url, array $params = [])
    {
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return $url;
    }
}