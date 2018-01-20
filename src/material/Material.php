<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\material;

use InvalidArgumentException;
use xutl\wechat\Api;
use xutl\wechat\message\Article;

class Material extends Api
{
    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'thumb', 'news_image'];

    const API_GET = 'https://api.weixin.qq.com/cgi-bin/material/get_material';
    const API_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/material/add_material';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/material/del_material';
    const API_STATS = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount';
    const API_LISTS = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material';
    const API_NEWS_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/material/add_news';
    const API_NEWS_UPDATE = 'https://api.weixin.qq.com/cgi-bin/material/update_news';
    const API_NEWS_IMAGE_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg';

    /**
     * Upload image.
     * @param string $path
     * @return mixed
     * @throws \yii\httpclient\Exception
     */
    public function uploadImage($path)
    {
        return $this->uploadMedia('image', $path);
    }

    /**
     * Upload voice.
     * @param string $path
     * @return mixed
     * @throws \yii\httpclient\Exception
     */
    public function uploadVoice($path)
    {
        return $this->uploadMedia('voice', $path);
    }

    /**
     * Upload thumb.
     * @param string $path
     * @return mixed
     * @throws \yii\httpclient\Exception
     */
    public function uploadThumb($path)
    {
        return $this->uploadMedia('thumb', $path);
    }

    /**
     * Upload video.
     * @param string $path
     * @param string $title
     * @param string $description
     * @return mixed
     * @throws \yii\httpclient\Exception
     */
    public function uploadVideo($path, $title, $description)
    {
        $params = [
            'description' => json_encode(
                [
                    'title' => $title,
                    'introduction' => $description,
                ], JSON_UNESCAPED_UNICODE),
        ];

        return $this->uploadMedia('video', $path, $params);
    }

    /**
     * Upload articles.
     * @param array|Article $articles
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function uploadArticle($articles)
    {
        if (!empty($articles['title']) || $articles instanceof Article) {
            $articles = [$articles];
        }

        $params = ['articles' => array_map(function ($article) {
            if ($article instanceof Article) {
                return $article->only([
                    'title', 'thumb_media_id', 'author', 'digest',
                    'show_cover_pic', 'content', 'content_source_url',
                ]);
            }
            return $article;
        }, $articles)];
        return $this->post(self::API_NEWS_UPLOAD, $params);
    }

    /**
     * Update article.
     * @param string $mediaId
     * @param string $article
     * @param int $index
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function updateArticle($mediaId, $article, $index = 0)
    {
        $params = [
            'media_id' => $mediaId,
            'index' => $index,
            'articles' => isset($article['title']) ? $article : (isset($article[$index]) ? $article[$index] : []),
        ];
        return $this->post(self::API_NEWS_UPDATE, $params);
    }

    /**
     * Upload image for article.
     * @param string $path
     * @return mixed
     * @throws \yii\httpclient\Exception
     */
    public function uploadArticleImage($path)
    {
        return $this->uploadMedia('news_image', $path);
    }

    /**
     * Fetch material.
     *
     * @param string $mediaId
     *
     * @return mixed
     */
    public function get1($mediaId)
    {
        /** @var \yii\httpclient\Response $response */
        $response = $request = $this->getHttpClient()->createRequest()
            ->setUrl([
                self::API_GET,
                'media_id' => $mediaId
            ])
            ->setMethod('GET')
            ->send();
        if ($response->isOk) {

        }
//        foreach ($response->getHeader('Content-Type') as $mime) {
//            if (preg_match('/(image|video|audio)/i', $mime)) {
//                return $response->getBody();
//            }
//        }
//
//        $json = $this->getHttp()->parseJSON($response);
//
//        // XXX: 微信开发这帮混蛋，尼玛文件二进制输出不带header，简直日了!!!
//        if (!$json) {
//            return $response->getBody();
//        }
//
//        $this->checkAndThrow($json);

        return $json;
    }

    /**
     * Delete material by media ID.
     * @param string $mediaId
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function delete($mediaId)
    {
        return $this->post(self::API_DELETE, ['media_id' => $mediaId]);
    }

    /**
     * List materials.
     *
     * example:
     *
     * {
     *   "total_count": TOTAL_COUNT,
     *   "item_count": ITEM_COUNT,
     *   "item": [{
     *             "media_id": MEDIA_ID,
     *             "name": NAME,
     *             "update_time": UPDATE_TIME
     *         },
     *         // more...
     *   ]
     * }
     *
     * @param string $type
     * @param int $offset
     * @param int $count
     *
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function lists($type, $offset = 0, $count = 20)
    {
        $params = [
            'type' => $type,
            'offset' => intval($offset),
            'count' => min(20, $count),
        ];
        return $this->post(self::API_LISTS, $params);
    }

    /**
     * Upload material.
     * @param string $type
     * @param string $path
     * @param array $form
     * @return mixed
     * @throws \yii\httpclient\Exception
     */
    protected function uploadMedia($type, $path, array $form = [])
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }
        $form['type'] = $type;
        return $this->upload($this->getAPIByType($type), ['media' => $path], $form);
    }

    /**
     * Get stats of materials.
     *
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public function stats()
    {
        return $this->get(self::API_STATS);
    }

    /**
     * Get API by type.
     *
     * @param string $type
     *
     * @return string
     */
    public function getAPIByType($type)
    {
        switch ($type) {
            case 'news_image':
                $api = self::API_NEWS_IMAGE_UPLOAD;
                break;
            default:
                $api = self::API_UPLOAD;
        }

        return $api;
    }
}