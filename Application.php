<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat;

use Yii;
use yii\web\User;
use yii\helpers\Url;
use yii\web\Session;
use yii\web\ErrorHandler;
use yii\authclient\Collection;
use yii\web\NotFoundHttpException;
use yii\base\InvalidRouteException;
use yii\web\UrlNormalizerRedirectException;

/**
 * Class Application
 * @property Collection $authClientCollection The auth client component. This property is read-only.
 * @package xutl\wechat
 */
class Application extends \yii\web\Application
{

    /**
     * Handles the specified request.
     * @param Request $request the request to be handled
     * @return Response the resulting response
     * @throws NotFoundHttpException if the requested route is invalid
     */
    public function handleRequest($request)
    {
        echo 999;
        exit;
        if (empty($this->catchAll)) {
            try {
                list($route, $params) = $request->resolve();
            } catch (UrlNormalizerRedirectException $e) {
                $url = $e->url;
                if (is_array($url)) {
                    if (isset($url[0])) {
                        // ensure the route is absolute
                        $url[0] = '/' . ltrim($url[0], '/');
                    }
                    $url += $request->getQueryParams();
                }
                return $this->getResponse()->redirect(Url::to($url, $e->scheme), $e->statusCode);
            }
        } else {
            $route = $this->catchAll[0];
            $params = $this->catchAll;
            unset($params[0]);
        }
        try {
            echo 999;
            exit;
            Yii::trace("Route requested: '$route'", __METHOD__);
            $this->requestedRoute = $route;
            $result = $this->runAction($route, $params);
            if ($result instanceof Response) {
                return $result;
            }

            $response = $this->getResponse();
            if ($result !== null) {
                $response->data = $result;
            }

            return $response;
        } catch (InvalidRouteException $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'), $e->getCode(), $e);
        }
    }

    /**
     * Returns the error handler component.
     * @return ErrorHandler the error handler application component.
     */
    public function getErrorHandler()
    {
        return $this->get('errorHandler');
    }

    /**
     * Returns the authClient component.
     * @return Collection the authClient application component.
     */
    public function getAuthClientCollection()
    {
        return $this->get('authClientCollection');
    }

    /**
     * Returns the request component.
     * @return Request the request component.
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Returns the response component.
     * @return Response the response component.
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * Returns the session component.
     * @return Session the session component.
     */
    public function getSession()
    {
        return $this->get('session');
    }

    /**
     * Returns the user component.
     * @return User the user component.
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'view' => [
                'class' => 'xutl\wechat\View',
            ],
            'request' => [
                'class' => 'xutl\wechat\Request',
                'parsers' => [
                    'application/json' => 'yii\web\JsonParser',
                    'text/json' => 'yii\web\JsonParser',
                ],
            ],
            'response' => ['class' => 'yii\wechat\Response'],
            'session' => ['class' => 'yii\web\Session'],
            'user' => ['class' => 'yii\web\User'],
            'errorHandler' => ['class' => 'yii\web\ErrorHandler'],
            'authClientCollection' => ['class' => 'yii\authclient\Collection'],
        ]);
    }
}