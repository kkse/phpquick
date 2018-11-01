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
     * @param mixed $definition 定义
     * @param bool $returnFail
     * @return mixed
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
}