<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\material;

use finfo;
use InvalidArgumentException;
use xutl\wechat\Api;
use yii\helpers\FileHelper;

/**
 * Class Temporary
 * 临时素材
 * @package xutl\wechat\material
 */
class Temporary extends Api
{

    /**
     * Allow media type.
     *
     * @var array
     */
    protected $allowTypes = ['image', 'voice', 'video', 'thumb'];

    const API_GET = 'https://api.weixin.qq.com/cgi-bin/media/get';
    const API_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/media/upload';

    /**
     * Download temporary material.
     *
     * @param string $mediaId
     * @param string $directory
     * @param string $filename
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function download($mediaId, $directory, $filename = '')
    {
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new InvalidArgumentException("Directory does not exist or is not writable: '$directory'.");
        }
        $filename = $filename ?: $mediaId;
        $stream = $this->getStream($mediaId);
        $finfo = new finfo(FILEINFO_MIME);
        $mime = strstr($finfo->buffer($stream), ';', true);
        $extensions = FileHelper::getExtensionsByMimeType($mime);
        $filename .= '.' . array_pop($extensions);
        $filePath = $directory . '/' . $filename;
        file_put_contents($filePath, $stream);
        return $filePath;
    }

    /**
     * Fetch item from WeChat server.
     *
     * @param string $mediaId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Core\Exceptions\RuntimeException
     */
    public function getStream($mediaId)
    {
        $response = $request = $this->getHttpClient()->createRequest()
            ->setUrl([
                self::API_GET,
                'media_id' => $mediaId
            ])
            ->setMethod('GET')
            ->send();
        if ($response->isOk) {
            $json = json_decode($response->content, true);
            if (JSON_ERROR_NONE === json_last_error()) {
                $this->checkAndThrow($json);
            }
        }
        return $response->content;
    }

    /**
     * Upload temporary material.
     * @param string $type
     * @param string $path
     * @return mixed
     */
    public function uploadMedia($type, $path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File does not exist, or the file is unreadable: '$path'");
        }
        if (!in_array($type, $this->allowTypes, true)) {
            throw new InvalidArgumentException("Unsupported media type: '{$type}'");
        }
        return $this->upload(self::API_UPLOAD, ['media' => $path], ['type' => $type]);
    }

    /**
     * Upload image.
     * @param $path
     * @return mixed
     */
    public function uploadImage($path)
    {
        return $this->uploadMedia('image', $path);
    }

    /**
     * Upload video.
     * @param string $path
     * @return mixed
     */
    public function uploadVideo($path)
    {
        return $this->uploadMedia('video', $path);
    }

    /**
     * Upload voice.
     * @param string $path
     * @return mixed
     */
    public function uploadVoice($path)
    {
        return $this->uploadMedia('voice', $path);
    }

    /**
     * Upload thumb.
     * @param string $path
     * @return mixed
     */
    public function uploadThumb($path)
    {
        return $this->uploadMedia('thumb', $path);
    }
}