<?php

namespace zlakomanoff\lazyparams;

use Yii;
use yii\base\ErrorException;

/**
 * Class Component
 * @package zlakomanoff\lazyparams
 */
class Component extends BaseObject
{

    /**
     * @throws ErrorException
     */
    public function init()
    {
        if (!array_key_exists($this->dbComponent, Yii::$app->components)) {
            throw new ErrorException('db component not found');
        }
        $this->db = &Yii::$app->{$this->dbComponent};
        return parent::init();
    }

    /**
     * @param string $name
     * @param array $params
     * @return null|string
     */
    public function __call($name, $params)
    {
        return $this->get($name, empty($params[0]) ? null : $params[0]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * @param mixed $offset
     * @return PhantomObject
     */
    public function offsetGet($offset)
    {
        return new PhantomObject($offset, $this);
    }

}