<?php

namespace xutl\wechat\url;

/**
 * Url.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */
use xutl\wechat\Api;

/**
 * Class Url.
 */
class Url extends Api
{
    const API_SHORTEN_URL = 'https://api.weixin.qq.com/cgi-bin/shorturl';

    /**
     * Shorten the url.
     *
     * @param string $url
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function shorten($url)
    {
        $params = [
            'action' => 'long2short',
            'long_url' => $url,
        ];
        return $this->json(self::API_SHORTEN_URL, $params);
    }
}
