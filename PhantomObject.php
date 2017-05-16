<?php

namespace zlakomanoff\lazyparams;

/**
 * Class PhantomObject
 * @package zlakomanoff\lazyparams
 */
final class PhantomObject extends BaseObject
{
    /**
     * @var string
     */
    private $value = '';

    /**
     * @var string|null
     */
    private $param = null;

    /**
     * PhantomObject constructor.
     * @param array $param
     * @param Component $parent
     */
    public function __construct($param, Component &$parent)
    {
        $this->param = $param;
        foreach (get_object_vars($parent) as $key => $value) {
            $this->$key = $value;
        }
        $this->value = $this->get($this->param);
        return parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return empty($this->value) ? '' : $this->value;
    }

    /**
     * @param mixed $offset
     * @return array|mixed|null
     */
    public function offsetGet($offset)
    {
        if (empty($this->value)) {
            if ($this->enableCache and $this->cacheDefaultValues) {
                $this->cacheSave($this->param, $offset);
            }
            if ($this->liquidMode) {
                $this->liquidSave($this->param, $offset);
            }
            return $offset;
        }
        return $this->value;
    }

}