<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\wechat\oauth;

use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\OAuth2;
use yii\base\Action;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\web\Response;

/**
 * AuthAction performs authentication via different auth clients.
 * It supports [[OAuth2]] client types.
 *
 * Usage:
 *
 * ```php
 * class SiteController extends Controller
 * {
 *     public function actions()
 *     {
 *         return [
 *             'auth' => [
 *                 'class' => 'xutl\wechat\oauth\AuthAction',
 *                 'successCallback' => [$this, 'successCallback'],
 *             ],
 *         ]
 *     }
 *
 *     public function successCallback($client)
 *     {
 *         $attributes = $client->getUserAttributes();
 *         // user login or signup comes here
 *     }
 * }
 * ```
 *
 * Usually authentication via external services is performed inside the popup window.
 * This action handles the redirection and closing of popup window correctly.
 *
 * @property string $cancelUrl Cancel URL.
 * @property string $successUrl Successful URL.
 */
class AuthAction extends Action
{
    /**
     * @var callable PHP callback, which should be triggered in case of successful authentication.
     * This callback should accept [[ClientInterface]] instance as an argument.
     * For example:
     *
     * ```php
     * public function onAuthSuccess($client)
     * {
     *     $attributes = $client->getUserAttributes();
     *     // user login or signup comes here
     * }
     * ```
     *
     * If this callback returns [[Response]] instance, it will be used as action response,
     * otherwise redirection to [[successUrl]] will be performed.
     *
     */
    public $successCallback;

    /**
     * @var string name or alias of the view file, which should be rendered in order to perform redirection.
     * If not set - default one will be used.
     */
    public $redirectView;

    /**
     * @var string the redirect url after successful authorization.
     */
    private $_successUrl = '';

    /**
     * @var string the redirect url after unsuccessful authorization (e.g. user canceled).
     */
    private $_cancelUrl = '';

    /**
     * @param string $url successful URL.
     */
    public function setSuccessUrl($url)
    {
        $this->_successUrl = $url;
    }

    /**
     * @return string successful URL.
     */
    public function getSuccessUrl()
    {
        if (empty($this->_successUrl)) {
            $this->_successUrl = $this->defaultSuccessUrl();
        }
        return $this->_successUrl;
    }

    /**
     * @param string $url cancel URL.
     */
    public function setCancelUrl($url)
    {
        $this->_cancelUrl = $url;
    }

    /**
     * @return string cancel URL.
     */
    public function getCancelUrl()
    {
        if (empty($this->_cancelUrl)) {
            $this->_cancelUrl = $this->defaultCancelUrl();
        }

        return $this->_cancelUrl;
    }

    /**
     * Creates default [[successUrl]] value.
     * @return string success URL value.
     */
    protected function defaultSuccessUrl()
    {
        return Yii::$app->getUser()->getReturnUrl();
    }

    /**
     * Creates default [[cancelUrl]] value.
     * @return string cancel URL value.
     */
    protected function defaultCancelUrl()
    {
        return Url::to(Yii::$app->getUser()->loginUrl);
    }

    /**
     * Runs the action.
     */
    public function run()
    {
        /** @var \xutl\wechat\oauth\OAuth $client */
        $client = Yii::$app->oauth;
        return $this->auth($client);
    }

    /**
     * This method is invoked in case of successful authentication via auth client.
     * @param ClientInterface $client auth client instance.
     * @throws InvalidConfigException on invalid success callback.
     * @return Response response instance.
     */
    protected function authSuccess($client)
    {
        if (!is_callable($this->successCallback)) {
            throw new InvalidConfigException('"' . get_class($this) . '::successCallback" should be a valid callback.');
        }
        $response = call_user_func($this->successCallback, $client);
        if ($response instanceof Response) {
            return $response;
        }
        return $this->redirectSuccess();
    }

    /**
     * Redirect to the given URL or simply close the popup window.
     * @param mixed $url URL to redirect, could be a string or array config to generate a valid URL.
     * @param bool $enforceRedirect indicates if redirect should be performed even in case of popup window.
     * @return \yii\web\Response response instance.
     */
    public function redirect($url, $enforceRedirect = true)
    {
        $viewFile = $this->redirectView;
        if ($viewFile === null) {
            $viewFile = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'redirect.php';
        } else {
            $viewFile = Yii::getAlias($viewFile);
        }
        $viewData = [
            'url' => $url,
            'enforceRedirect' => $enforceRedirect,
        ];
        $response = Yii::$app->getResponse();
        $response->content = Yii::$app->getView()->renderFile($viewFile, $viewData);
        return $response;
    }

    /**
     * Redirect to the URL. If URL is null, [[successUrl]] will be used.
     * @param string $url URL to redirect.
     * @return \yii\web\Response response instance.
     */
    public function redirectSuccess($url = null)
    {
        if ($url === null) {
            $url = $this->getSuccessUrl();
        }
        return $this->redirect($url);
    }

    /**
     * Redirect to the [[cancelUrl]] or simply close the popup window.
     * @param string $url URL to redirect.
     * @return \yii\web\Response response instance.
     */
    public function redirectCancel($url = null)
    {
        if ($url === null) {
            $url = $this->getCancelUrl();
        }
        return $this->redirect($url, false);
    }

    /**
     * Performs OAuth2 auth flow.
     * @param OAuth2 $client auth client instance.
     * @return Response action response.
     * @throws \yii\base\Exception on failure.
     */
    protected function auth($client)
    {
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'access_denied') {
                // user denied error
                return $this->redirectCancel();
            } else {
                // request error
                if (isset($_GET['error_description'])) {
                    $errorMessage = $_GET['error_description'];
                } elseif (isset($_GET['error_message'])) {
                    $errorMessage = $_GET['error_message'];
                } else {
                    $errorMessage = http_build_query($_GET);
                }
                throw new Exception('Auth error: ' . $errorMessage);
            }
        }

        // Get the access_token and save them to the session.
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $token = $client->fetchAccessToken($code);
            if (!empty($token)) {
                return $this->authSuccess($client);
            } else {
                return $this->redirectCancel();
            }
        } else {
            $url = $client->buildAuthUrl();
            return Yii::$app->getResponse()->redirect($url);
        }
    }
}