<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\oauth;

use Yii;

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
class AuthAction extends \yii\authclient\AuthAction
{
    /**
     * Runs the action.
     * @return \yii\web\Response
     * @throws \yii\base\NotSupportedException
     */
    public function run()
    {
        if (Yii::$app->user->isGuest) {
            $client = Yii::$app->wechat->oauth;
            return $this->auth($client);
        } else {
            return $this->controller->goHome();
        }
    }
}