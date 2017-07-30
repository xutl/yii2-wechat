<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\wechat\message;

/**
 * Class AbstractMessage.
 */
abstract class AbstractMessage extends Attribute
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type;

    /**
     * Message id.
     *
     * @var int
     */
    protected $id;

    /**
     * Message target user open id.
     *
     * @var string
     */
    protected $to;

    /**
     * Message sender open id.
     *
     * @var string
     */
    protected $from;

    /**
     * Message attributes.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Return type name message.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Magic getter.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        return parent::__get($property);
    }

    /**
     * Magic setter.
     *
     * @param string $property
     * @param mixed $value
     *
     * @return AbstractMessage
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            parent::__set($property, $value);
        }

        return $this;
    }
}