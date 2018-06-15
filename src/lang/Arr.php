<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/3/27
 * Time: 18:06
 */

namespace kkse\quick\lang;

/**
 * 数组的简便操作
 * Class Arr
 * @package kkse\quick\lang
 */
final class Arr
{
    //可以修改默认配置
    public static $get_val_default_rule = [
        'default' => null,
        'judge' => self::JUDGE_MODE_ISSET,
    ];

    const JUDGE_MODES = [self::JUDGE_MODE_ISSET,self::JUDGE_MODE_NO_EMPTY];
    const JUDGE_MODE_ISSET = 'isset';
    const JUDGE_MODE_NO_EMPTY = 'no_empty';

    /**
     * @param array $data
     * @param array ...$keys 最后一个如果是数组，就当做规则处理
     * @return mixed
     */
    public static function getVal(array $data,  ...$keys)
    {
        $rule = end($keys);
        if (is_array($rule)) {
            array_pop($keys);
        } else {
            $rule = [];
        }

        $rule += self::$get_val_default_rule;
        in_array($rule['judge'], self::JUDGE_MODES) or $rule['judge'] = self::$get_val_default_rule['judge'];
        foreach ($keys as $key) {
            if (self::judge($data, $key, $rule['judge'])) {
                return $data[$key];
            }
        }
        return $rule['default'];
    }

    /**
     * 判断函数，目前只判断isset与!empty。
     * @param array $data
     * @param $key
     * @param $judge
     * @return bool
     */
    public static function judge(array $data, $key, $judge){
        switch ($judge) {
            case self::JUDGE_MODE_ISSET:
                return isset($data[$key]);
            case self::JUDGE_MODE_NO_EMPTY:
                return !empty($data[$key]);
        }
        return  false;
    }
}