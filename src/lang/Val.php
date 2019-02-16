<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/2/12
 * Time: 17:22
 */

namespace kkse\quick\lang;

/**
 * 对通常数值进行处理
 * Class Val
 * @package kkse\quick\lang
 */
final class Val
{
    /**
     * @param $prefix
     * @return mixed
     */
    public static function uuid($prefix = '')
    {
        return str_replace('.', '', uniqid($prefix, true));
    }

    /**
     * 只要正数的函数
     * @param mixed $val
     * @return int
     */
    public static function uintval($val)
    {
        $val = intval($val);
        return $val > 0 ? $val : 0;
    }

    /**
     * 只返回1和0
     * @param $val
     * @return int
     */
    public static function bintval($val)
    {
        $val = intval($val);
        return $val ? 1 : 0;
    }

    /**
     * 分转元
     * @param string|int $fen
     * @return string
     */
    public static function toYuanPrice($fen)
    {
        return bcdiv($fen, 100, 2);
    }

    /**
     * 元转分
     * @param string|float $yuan
     * @return int
     */
    public static function toFenPrice($yuan)
    {
        return intval(bcmul($yuan, 100, 0));
    }

    /**
     * 根据定义获取动态数据的值
     * @param $definition
     * @param bool $returnFail
     * @return bool|mixed|null|object
     * @throws \ReflectionException
     */
    public static function getDynamicVal($definition, $returnFail = false)
    {
        return DefinitionVal::formatVal($definition, $returnFail);
    }

    /**
     * 判断是否动态数据
     * @param $definition
     * @return bool
     */
    public static function isDynamicVal($definition)
    {
        $obj = DefinitionVal::format($definition);
        if (!$obj || $obj->getType() == DefinitionVal::TYPE_VALUE) {
            return false;
        }
        return true;
    }


    /**
     * 字符串命名风格转换
     * type 0 将 Java 风格转换为 C 的风格 1 将 C 风格转换为 Java 的风格
     * @access public
     * @param  string  $name    字符串
     * @param  integer $type    转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}