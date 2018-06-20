<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/6/15
 * Time: 14:29
 */

namespace kkse\quick\lang\scalar;

/**
 * 货币处理
 * Class Currency
 * @package kkse\quick\lang\scalar
 */
class Currency extends Number
{
    /**
     * 元转分
     * @param $val
     * @return string
     */
    public static function yuanToFen($val)
    {
        $val = bcmul($val, 100, 1);
        return self::round($val, 0);
    }

    /**
     * 分转元
     * @param $val
     * @return string
     */
    public static function fenToYuan($val)
    {
        $val = bcdiv($val, 100, 3);
        return self::round($val, 2);
    }
}