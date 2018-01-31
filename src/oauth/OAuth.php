<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\oauth;

use Yii;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;

/**
 * Class OAuth
 * @package xutl\wechat\oauth
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class OAuth extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * @inheritdoc
     */
    public $refreshTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://api.weixin.qq.com';

    /**
     * @var bool 是否使用openid
     */
    public $useOpenId = true;

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'wechat';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return Yii::t('app', 'Wechat');
    }

    /**
     * 初始化组件
     */
    public function init()
    {
        parent::init();
        if (empty ($this->clientId)) {
            $this->clientId = Yii::$app->wechat->appId;
        }
        if (empty ($this->clientSecret)) {
            $this->clientSecret = Yii::$app->wechat->appSecret;
        }
        if ($this->scope === null) {
            $this->scope = 'snsapi_userinfo';
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'id' => $this->useOpenId ? 'openid' : 'unionid',
            'username' => 'nickname',
        ];
    }

    /**
     * Composes user authorization URL.
     * @param array $params additional auth GET params.
     * @return string authorization URL.
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'redirect_uri' => $this->getReturnUrl(),
            'response_type' => 'code',
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        if ($this->validateAuthState) {
            $authState = $this->generateAuthState();
            $this->setState('authState', $authState);
            $defaultParams['state'] = $authState;
        }
        $authUrl = $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
        return $authUrl . '#wechat_redirect';
    }

    /**
     * Composes URL from base URL and GET params.
     * @param string $url base URL.
     * @param array $params GET params.
     * @return string composed URL.
     */
    protected function composeUrl($url, array $params = [])
    {
        if (!empty($params)) {
            if (strpos($url, '?') === false) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            $url .= http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }
        return $url;
    }

    /**
     * @param \yii\httpclient\Request $request HTTP request instance.
     * @param OAuthToken $accessToken access token instance.
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();
        $data['access_token'] = $accessToken->getToken();
        $data['openid'] = $accessToken->getParam('openid');
        $request->setData($data);
    }

    /**
     * Applies client credentials (e.g. [[clientId]] and [[clientSecret]]) to the HTTP request instance.
     * This method should be invoked before sending any HTTP request, which requires client credentials.
     * @param \yii\httpclient\Request $request HTTP request instance.
     * @since 2.1.3
     */
    protected function applyClientCredentialsToRequest($request)
    {
        $request->addData([
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
        ]);
    }

    /**
     * 返回OpenId
     * @return mixed
     */
    public function getOpenId()
    {
        return $this->getAccessToken()->getParam('openid');
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('sns/userinfo', 'GET', [
            //'lang'=>
        ]);
    }

    /**
     * Gets new auth token to replace expired one.
     * @param OAuthToken $token expired auth token.
     * @return OAuthToken new auth token.
     * @throws \yii\authclient\InvalidResponseException
     */
    public function refreshAccessToken(OAuthToken $token)
    {
        $params = [
            'grant_type' => 'refresh_token'
        ];
        $params = array_merge($token->getParams(), $params);

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->refreshTokenUrl)
            ->setData($params);

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }
}