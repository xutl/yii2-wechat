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
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\base\InvalidConfigException;
use yii\httpclient\RequestEvent;
use xutl\wechat\AccessToken;
use yii\web\HttpException;

/**
 * Class BaseApi
 * @package xutl\api
 */
abstract class Api extends Component
{
    /**
     * @var Client
     */
    public $_httpClient;

    /**
     * The request token.
     *
     * @var AccessToken AccessToken 实例
     */
    protected $accessToken = 'accessToken';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->accessToken !== null) {
            $this->accessToken = Instance::ensure($this->accessToken, AccessToken::class);
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
            $this->_httpClient->on(Client::EVENT_BEFORE_SEND, function (RequestEvent $event) {
                $url = $event->request->getUrl();
                if (is_array($url)) {
                    $url = array_merge($url, [$this->accessToken->queryName => $this->accessToken->getToken()]);
                } else {
                    $url = [$url, $this->accessToken->queryName => $this->accessToken->getToken()];
                }
                $event->request->setUrl($url);
            });
        }
        return $this->_httpClient;
    }

    /**
     * GET request.
     *
     * @param string|array $url use a string to represent a URL (e.g. `http://some-domain.com`, `item/list`),
     * or an array to represent a URL with query parameters (e.g. `['item/list', 'param1' => 'value1']`).
     * @return array
     * @throws Exception
     */
    public function get($url)
    {
        return $this->sendRequest('GET', $url);
    }

    /**
     * POST request.
     *
     * @param string|array $url use a string to represent a URL (e.g. `http://some-domain.com`, `item/list`),
     * or an array to represent a URL with query parameters (e.g. `['item/list', 'param1' => 'value1']`).
     * @param array|string $params
     * @return array
     * @throws Exception
     */
    public function post($url, $params = [])
    {
        return $this->sendRequest('POST', $url, $params);
    }

    /**
     * JSON request.
     *
     * @param string|array $url use a string to represent a URL (e.g. `http://some-domain.com`, `item/list`),
     * or an array to represent a URL with query parameters (e.g. `['item/list', 'param1' => 'value1']`).
     * @param string|array $params
     *
     * @return array
     */
    public function json($url, $params = [])
    {
        if (is_array($params)) {
            $params = Json::encode($params);
        }
        return $this->sendRequest('POST', $url, $params, [
            'content-type' => 'application/json'
        ]);
    }

    /**
     * Upload file.
     *
     * @param string|array $url use a string to represent a URL (e.g. `http://some-domain.com`, `item/list`),
     * or an array to represent a URL with query parameters (e.g. `['item/list', 'param1' => 'value1']`).
     * @param array $files
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function upload($url, array $files = [], array $params = [])
    {
        return $this->sendRequest('POST', $url, $params, [], $files);
    }


    /**
     * Sends HTTP request.
     * @param string $method request type.
     * @param string|array $url use a string to represent a URL (e.g. `http://some-domain.com`, `item/list`),
     * or an array to represent a URL with query parameters (e.g. `['item/list', 'param1' => 'value1']`).
     * @param string|array $params request params.
     * @param array $headers additional request headers.
     * @param array $files
     * @return array response.
     * @throws Exception on failure.
     */
    protected function sendRequest($method, $url, $params = [], array $headers = [], $files = [])
    {
        $request = $this->getHttpClient()->createRequest()
            ->setUrl($url)
            ->setMethod($method)
            ->setHeaders($headers);
        if (is_array($params)) {
            $request->setData($params);
        } else {
            $request->setContent($params);
        }
        foreach ($files as $name => $fileName) {
            $request->addFile($name, $fileName);
        }
        $response = $request->send();
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

    /**
     * Check the array data errors, and Throw exception when the contents contains error.
     * @param array $contents
     * @throws HttpException
     */
    protected function checkAndThrow(array $contents)
    {
        if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }

            throw new HttpException($contents['errmsg'], $contents['errcode']);
        }
    }
}