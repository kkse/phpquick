<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/7/2
 * Time: 15:16
 */

namespace kkse\quick\lang\scalar;

/**
 * 时间扩展处理
 * Class DateTimeVal
 * @package kkse\quick\lang\scalar
 */
class DateTimeVal
{
    /**
     * @param string $format
     * @param string|int|bool $timestamp
     * @param bool $isTimestamp
     * @return string
     */
    public static function format(string $format, $timestamp = false, $isTimestamp = false) {
        if ($timestamp) {
            if ($isTimestamp) {
                $timestamp = intval($timestamp);
            } else {
                $timestamp = strtotime($timestamp);
            }
            if (!$timestamp) {
                return '';
            }
        } else {
            $timestamp = time();
        }

        return date($format, $timestamp)?:'';
    }

}