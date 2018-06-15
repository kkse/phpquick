<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/2/23
 * Time: 22:46
 */

namespace kkse\quick\lang;


class Constant
{

    /**
     * 批量定义常量
     * @param array $constant_map
     * @param bool $isReturn
     * @return string
     */
    public static function batchDefine(array $constant_map, bool $isReturn = false)
    {
        if ($isReturn) {
            $str = 'namespace{'.PHP_EOL;
            foreach ($constant_map as $name => $definition) {
                if (Val::isDynamicVal($definition)) {
                    $str .= 'define('.var_export($name, true).', kkse\quick\lang\Val::getDynamicVal('.var_export($definition, true).'));'.PHP_EOL;
                } else {
                    $str .= 'define('.var_export($name, true).', '.var_export(Val::getDynamicVal($definition), true).');'.PHP_EOL;
                }
            }
            $str .= '}'.PHP_EOL;
            return $str;
        } else {
            foreach ($constant_map as $name => $definition) {
                define($name, Val::getDynamicVal($definition));
            }
        }
        return '';
    }
}