<?php
namespace kkse\quick\lang\scalar;

/**
 * 数字操作
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/6/15
 * Time: 14:00
 */
class Number
{
    //将一个数字值转为字符串数字
    public static function round($val, $scale = 0) {
        if ($scale < 0) {
            $beishu = bcpow('10', -$scale);
            $val = bcdiv($val, $beishu, 1);
            $val = self::round($val, 0);
            return bcmul($val, $beishu, 0);
        }

        $ret = bcadd($val, 0, $scale+1);

        if (substr($val, -1) >= 5) {
            $ret = bcadd($val, bcpow('0.1', $scale, $scale), $scale);
        } else {
            $ret = substr($ret, 0, -1);
        }

        if ($scale > 0) {//当有小数点的时候，去掉后面的0和.
            $ret = rtrim($ret, '0');
        }

        $ret = rtrim($ret, '.');
        return $ret;
    }


}