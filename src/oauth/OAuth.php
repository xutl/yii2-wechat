<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\oauth;

use Yii;
use xutl\authclient\WeChat;
use yii\authclient\OAuthToken;

/**
 * Class OAuth
 * @package xutl\wechat\oauth
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 1.0
 */
class OAuth extends WeChat
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
    public function init()
    {
        if (empty ($this->clientId)) {
            $this->clientId = Yii::$app->wechat->appId;
        }
        if (empty ($this->clientSecret)) {
            $this->clientSecret = Yii::$app->wechat->appSecret;
        }
        if ($this->scope === null) {
            $this->scope = 'snsapi_userinfo';
        }
        parent::init();
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
     * Composes user authorization URL.
     * @param array $params additional auth GET params.
     * @return string authorization URL.
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->getReturnUrl(),
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
}