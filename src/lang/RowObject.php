<?php
namespace kkse\quick\lang;


abstract class RowObject implements \ArrayAccess, \Serializable, \JsonSerializable
{
    protected $_data;//存储的数据
    protected $_setx = [];//允许设置的key

    public function __construct(array $data)
    {
        $this->_init($data);
    }

    protected function _init(array $data)
    {
        $this->_data = $data;
        foreach ($data as $key => $val) {
            property_exists($this, $key) and $this->$key = $val;
        }
    }


    /**
     * @param $method
     * @param $args
     * @return null
     * @throws QuickException
     */
    public function __call($method, $args)
    {
        if (strtolower(substr($method, 0, 3))=='get') {
            $field   =   Val::parseName(substr($method, 3));
            return $this->__get($field);
        } else {
            QuickException::throwError(__CLASS__.':'.$method.'方法不存在');
            return null;
        }
    }

    public function __get($offset)
    {
        return isset($this->_data[$offset])?$this->_data[$offset]:null;
    }

    public function __set($offset, $value)
    {
        if (in_array($offset, $this->_setx)) {
            $this->_data[$offset] = $value;
            property_exists($this, $offset) and $this->$offset = $value;
        }
    }

    //=======================================================

    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset)
    {
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function serialize()
    {
        return serialize($this->_data);
    }

    /**
     * @param string $data
     * @throws \Exception
     */
    public function unserialize($data)
    {
        $data = unserialize($data);
        if (!is_array($data)) {
            throw new \Exception('fail data');
        }
        $this->_init($data);
    }

    public function toArray()
    {
        return $this->_data;
    }

    protected function _updateData(array $update)
    {
        $this->_data = $update + $this->_data;
        foreach ($update as $key => $val) {
            property_exists($this, $key) and $this->$key = $val;
        }
    }

    public function jsonSerialize() {
        return $this->_data;
    }
}
