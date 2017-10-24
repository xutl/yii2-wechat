<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\notice;


use xutl\wechat\Api;
use InvalidArgumentException;

/**
 * Class Notice
 * @package xutl\wechat\notice
 */
class Notice extends Api
{
    /**
     * Default color.
     *
     * @var string
     */
    protected $defaultColor = '#173177';

    /**
     * Attributes.
     *
     * @var array
     */
    protected $message = [
        'touser' => '',
        'template_id' => '',
        'url' => '',
        'data' => [],
    ];

    /**
     * Required attributes.
     *
     * @var array
     */
    protected $required = ['touser', 'template_id'];

    /**
     * Message backup.
     *
     * @var array
     */
    protected $messageBackup;

    const API_SEND_NOTICE = 'https://api.weixin.qq.com/cgi-bin/message/template/send';
    const API_SET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry';
    const API_ADD_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template';
    const API_GET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/get_industry';
    const API_GET_ALL_PRIVATE_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template';
    const API_DEL_PRIVATE_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/del_private_template';

    /**
     * 初始化组件
     */
    public function init()
    {
        parent::init();
        $this->messageBackup = $this->message;
    }

    /**
     * Set default color.
     *
     * @param string $color example: #0f0f0f
     *
     * @return $this
     */
    public function defaultColor($color)
    {
        $this->defaultColor = $color;
        return $this;
    }

    /**
     * 设置所属行业
     *
     * @param int $industryOne 公众号模板消息所属行业编号
     * @param int $industryTwo 公众号模板消息所属行业编号
     * @return array
     */
    public function setIndustry($industryOne, $industryTwo)
    {
        $params = [
            'industry_id1' => $industryOne,
            'industry_id2' => $industryTwo,
        ];
        return $this->json(static::API_SET_INDUSTRY, $params);
    }

    /**
     * 获取设置的行业信息
     * @return array
     */
    public function getIndustry()
    {
        return $this->get(static::API_GET_INDUSTRY);
    }

    /**
     * 获得模板ID
     *
     * @param string $shortId
     * @return array
     */
    public function addTemplate($shortId)
    {
        $params = ['template_id_short' => $shortId];
        return $this->json(static::API_ADD_TEMPLATE, $params);
    }

    /**
     * 获取模板列表
     *
     * @return array
     */
    public function getPrivateTemplates()
    {
        return $this->get(static::API_GET_ALL_PRIVATE_TEMPLATE);
    }

    /**
     * 删除模板
     *
     * @param string $templateId
     *
     * @return array
     */
    public function deletePrivateTemplate($templateId)
    {
        $params = ['template_id' => $templateId];
        return $this->json(static::API_DEL_PRIVATE_TEMPLATE, $params);
    }

    /**
     * 发送模板消息
     *
     * @param $data
     * @param array $data
     * @return array
     */
    public function send(array $data = [])
    {
        $params = array_merge($this->message, $data);
        foreach ($params as $key => $value) {
            if (in_array($key, $this->required, true) && empty($value) && empty($this->message[$key])) {
                throw new InvalidArgumentException("Attribute '$key' can not be empty!");
            }
            $params[$key] = empty($value) ? $this->message[$key] : $value;
        }
        $params['data'] = $this->formatData($params['data']);
        $this->message = $this->messageBackup;
        return $this->json(static::API_SEND_NOTICE, $params);
    }

    /**
     * Magic access..
     *
     * @param string $method
     * @param array $args
     *
     * @return Notice
     */
    public function __call($method, $args)
    {
        $map = [
            'template' => 'template_id',
            'templateId' => 'template_id',
            'uses' => 'template_id',
            'to' => 'touser',
            'receiver' => 'touser',
            'url' => 'url',
            'link' => 'url',
            'data' => 'data',
            'with' => 'data',
            'formId' => 'form_id',
            'prepayId' => 'form_id',
        ];
        if (0 === stripos($method, 'with') && strlen($method) > 4) {
            $method = lcfirst(substr($method, 4));
        }
        if (0 === stripos($method, 'and')) {
            $method = lcfirst(substr($method, 3));
        }
        if (isset($map[$method])) {
            $this->message[$map[$method]] = array_shift($args);
        }
        return $this;
    }

    /**
     * 格式化模板数据
     *
     * @param array $data
     *
     * @return array
     */
    protected function formatData($data)
    {
        $return = [];
        foreach ($data as $key => $item) {
            if (is_scalar($item)) {
                $value = $item;
                $color = $this->defaultColor;
            } elseif (is_array($item) && !empty($item)) {
                if (isset($item['value'])) {
                    $value = strval($item['value']);
                    $color = empty($item['color']) ? $this->defaultColor : strval($item['color']);
                } elseif (count($item) < 2) {
                    $value = array_shift($item);
                    $color = $this->defaultColor;
                } else {
                    list($value, $color) = $item;
                }
            } else {
                $value = 'error data item.';
                $color = $this->defaultColor;
            }
            $return[$key] = [
                'value' => $value,
                'color' => $color,
            ];
        }
        return $return;
    }
}