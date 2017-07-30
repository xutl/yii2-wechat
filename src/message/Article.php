<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\message;

/**
 * Class Article
 * @package xutl\wechat\message
 */
class Article extends AbstractMessage
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'thumb_media_id',
        'author',
        'title',
        'content',
        'digest',
        'source_url',
        'show_cover',
    ];

    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected $aliases = [
        'source_url' => 'content_source_url',
        'show_cover' => 'show_cover_pic',
    ];
}