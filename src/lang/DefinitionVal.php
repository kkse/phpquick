<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/2/23
 * Time: 21:29
 */

namespace kkse\quick\lang;

/**
 * 定义一个值
 * Class DefinitionVal
 * @package kkse\quick\lang
 */
class DefinitionVal
{
    const TYPE_VALUE = 'value';//普通的值
    const TYPE_FUNCTION = 'function';//方法函数调用
    const TYPE_OBJECT = 'object';//类对象初始化
    const TYPE_VARIABLE = 'variable';//变量获取,类的话只支持类的静态变量
    const TYPE_CONSTANT = 'constant';//常量获取,支持类常量

    protected $_type = '';
    protected $_class = '';
    protected $_obj = null;
    protected $_func = '';
    protected $_name = '';
    protected $_params = [];
    protected $_val = null;
    protected $docall = false;
    private function __construct()
    {
    }

    /**
     * @param $definition
     * @return self|null
     */
    public static function format($definition)
    {
        if ($definition instanceof self) {
            return $definition;
        } elseif (is_array($definition) && isset($definition['__type__'])) {
            $obj = new self();
            $obj->_type = $definition['__type__'];
            switch ($definition['__type__']) {
                case self::TYPE_VALUE:
                    if (!isset($definition['value'])) {
                        return null;
                    }
                    $obj->_val = $definition['value'];
                    $obj->docall = true;
                    break;
                case self::TYPE_FUNCTION:
                    if (!isset($definition['func'])) {
                        return null;
                    }

                    if (isset($definition['obj']) && is_array($definition['obj']) && isset($definition['obj']['__type__'])) {
                        $obj->_obj = $definition['obj'];
                    }

                    if (isset($definition['class']) && class_exists($definition['class'])) {
                        $obj->_class = $definition['class'];
                    }

                    //|| !is_callable($definition['func'])

                    $obj->_func = $definition['func'];

                    if (!empty($definition['params']) && is_array($definition['params'])) {
                        $obj->_params = $definition['params'];
                    }
                    break;
                case self::TYPE_OBJECT:
                    if (!isset($definition['class']) || !class_exists($definition['class'])) {
                        return null;
                    }

                    $obj->_class = $definition['class'];
                    if (!empty($definition['params']) && is_array($definition['params'])) {
                        $obj->_params = $definition['params'];
                    }

                    break;
                case self::TYPE_VARIABLE:
                    if (!isset($definition['name']) || !is_string($definition['name'])) {
                        return null;
                    }
                    $obj->_name = $definition['name'];

                    if (!empty($definition['class'])) {
                        if (!class_exists($definition['class'])) {
                            return null;
                        }
                        $obj->_class = $definition['class'];
                    }

                    if (!empty($definition['params']) && is_array($definition['params'])) {
                        $obj->_params = $definition['params'];
                    }
                    break;
                case self::TYPE_CONSTANT:
                    if (!isset($definition['name']) || !is_string($definition['name'])) {
                        return null;
                    }
                    $obj->_name = $definition['name'];

                    if (!empty($definition['class'])) {
                        if (!class_exists($definition['class'])) {
                            return null;
                        }
                        $obj->_class = $definition['class'];
                    }
                    break;
                default:
                    return null;
            }
            return $obj;
        }

        return null;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function getParamVals()
    {
        $params = [];
        foreach($this->_params as $param) {
            $params[] = self::formatVal($param);
        }

        return $params;
    }

    /**
     * @return mixed|null|object
     * @throws \ReflectionException
     */
    public function getVal()
    {
        if (!$this->docall) {
            $this->docall = true;
            switch ($this->_type) {
                case self::TYPE_FUNCTION:
                    if ($this->_obj) {
                        $obj = self::formatVal($this->_obj, true);
                        if (is_object($obj)) {
                            $this->_val = call_user_func_array([$obj, $this->_func], $this->getParamVals());
                        }
                    } elseif ($this->_class) {
                        $this->_val = call_user_func_array([$this->_class, $this->_func], $this->getParamVals());
                    } else {
                        $this->_val = call_user_func_array($this->_func, $this->getParamVals());
                    }
                    break;
                case self::TYPE_OBJECT:
                    $refclass = new \ReflectionClass($this->_class);
                    $this->_val = $refclass->newInstanceArgs($this->getParamVals());
                    break;
                case self::TYPE_VARIABLE:
                    if ($this->_class) {
                        $refProperty = new \ReflectionProperty($this->_class, $this->_name);
                        $this->_val = $refProperty->getValue();
                    } else {
                        $this->_val = $GLOBALS[$this->_name];
                    }
                    break;
                case self::TYPE_CONSTANT:
                    if ($this->_class) {
                        $refclass = new \ReflectionClass($this->_class);
                        $this->_val = $refclass->getConstant($this->_name);
                    } else {
                        $this->_val = constant($this->_name);
                    }
                    break;
            }
        }
        return $this->_val;
    }

    public function getType()
    {
        return $this->_type;
    }

    /**
     * 根据定义获取动态数据的值
     * @param $definition
     * @param bool $returnFail
     * @return bool|mixed|null|object
     * @throws \ReflectionException
     */
    public static function formatVal($definition, $returnFail = false)
    {
        $obj = self::format($definition);
        if ($obj) {
            return $obj->getVal();
        }

        if ($returnFail) {
            return false;
        }

        //只要不是定义格式的，就原值返回
        return $definition;
    }
}