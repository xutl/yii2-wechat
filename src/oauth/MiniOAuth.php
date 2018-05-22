<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\oauth;

use Yii;
use yii\authclient\OAuth2;
use yii\web\HttpException;

/**
 * 微信小程序定制
 * @package xutl\wechat\oauth
 */
class MiniOAuth extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://api.weixin.qq.com/sns/jscode2session';

    /**
     * @var bool 是否使用openid
     */
    public $useOpenId = true;

    /**
     * 获取Token
     * @param string $authCode
     * @param array $params
     * @return \yii\authclient\OAuthToken
     * @throws \yii\authclient\InvalidResponseException
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'js_code' => $authCode,
            'grant_type' => 'authorization_code'
        ];

        $request = $this->createRequest()
            ->setMethod('GET')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * Creates token from its configuration.
     * @param array $tokenConfig token configuration.
     * @return \yii\authclient\OAuthToken token instance.
     */
    protected function createToken(array $tokenConfig = [])
    {
        $tokenConfig['tokenParamKey'] = 'session_key';

        return parent::createToken($tokenConfig);
    }

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'id' => $this->useOpenId ? 'openid' : 'unionid',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return [];
    }
}