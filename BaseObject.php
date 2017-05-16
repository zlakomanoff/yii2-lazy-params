<?php

namespace zlakomanoff\lazyparams;

use ArrayAccess;
use Yii;
use yii\base\Component;
use yii\db\Query;

/**
 * Class BaseObject
 * @package zlakomanoff\lazyparams
 */
class BaseObject extends Component implements ArrayAccess
{

    /**
     * @var string
     */
    public $tableName = 'lazy_params';

    /**
     * @var string
     */
    public $keyColumn = 'key';

    /**
     * @var string
     */
    public $valueColumn = 'value';

    /**
     * @var bool
     */
    public $liquidMode = false;

    /**
     * @var bool
     */
    public $enableCache = false;

    /**
     * @var bool
     */
    public $cacheDefaultValues = false;

    /**
     * @var string
     */
    public $cacheComponent = 'cache';

    /**
     * @var string
     */
    public $dbComponent = 'db';

    /**
     * @var \yii\db\Connection
     */
    protected $db;

    /**
     * @var bool
     */
    protected $fromCache = false;

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return null;
    }

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetExists($offset)
    {
        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return null
     */
    public function offsetSet($offset, $value)
    {
        return null;
    }

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetUnset($offset)
    {
        return null;
    }

    /**
     * @param null $param
     * @param string $defaultValue
     * @return false|null|string
     */
    public function get($param = null, $defaultValue = '')
    {
        $result = '';
        $this->fromCache = false;

        if (empty($param)) {
            return $result;
        }

        if ($this->enableCache) {
            $result = $this->cacheGet($param);
        }

        if (empty($result)) {
            $result = (new Query())
                ->select($this->valueColumn)
                ->from($this->tableName)
                ->where([$this->keyColumn => $param])
                ->scalar($this->db);
        }

        // interact with default value

        if (empty($result)) {
            if (!$this->fromCache and $this->enableCache and $this->cacheDefaultValues) {
                $this->cacheSave($param, $defaultValue);
            }

            if ($this->liquidMode) {
                $this->liquidSave($param, $defaultValue);
            }

            return $defaultValue;
        }

        // regular value from db or cache

        if (!$this->fromCache) {
            if ($this->enableCache) {
                $this->cacheSave($param, $result);
            }
        }

        return $result;

    }

    /**
     * @param $param
     * @return mixed
     */
    protected function cacheGet($param)
    {
        /** @var \yii\caching\Cache $cache */
        $cache = Yii::$app->{$this->cacheComponent};
        if ($result = $cache->get($param)) {
            $this->fromCache = true;
        }
        return $result;
    }

    /**
     * @param $param
     * @param $value
     * @return bool
     */
    protected function cacheSave($param, $value = null)
    {
        if (empty($value)) {
            return false;
        }
        /** @var \yii\caching\Cache $cache */
        $cache = Yii::$app->{$this->cacheComponent};
        return $cache->set($param, $value);
    }

    /**
     * @param $param
     * @param $value
     * @return int
     */
    protected function liquidSave($param, $value = null)
    {
        if (empty($value)) {
            return false;
        }
        $bindVariables = [
            $this->keyColumn => $param,
            $this->valueColumn => $value
        ];
        return $this->db->createCommand()->insert($this->tableName, $bindVariables)->execute();
    }

}