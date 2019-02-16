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
    protected $dt;

    public function __construct(\DateTime $dt = null)
    {
        $dt or $dt = new \DateTime();
        $this->dt = clone $dt;
    }

    public function getDateTime()
    {
        return clone $this->dt;
    }

    /**
     * @param $func
     * @param $val
     * @param $unit
     * @param $prefix
     * @throws \Exception
     */
    protected function _changeDT($func, $val, $unit, $prefix){
        $invert = $val < 0 ? 1 : 0;
        $val = abs($val);
        $di = new \DateInterval("{$prefix}{$val}{$unit}");
        $di->invert = $invert;
        $this->dt->$func($di);
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function addSecond($val = 1)
    {
        $val and $this->_changeDT('add', $val, 'S', 'PT');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function subSecond($val = 1)
    {
        $val and $this->_changeDT('sub', $val, 'S', 'PT');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function addMinute($val = 1)
    {
        $val and $this->_changeDT('add', $val, 'M', 'PT');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function subMinute($val = 1)
    {
        $val and $this->_changeDT('sub', $val, 'M', 'PT');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function addHour($val = 1)
    {
        $val and $this->_changeDT('add', $val, 'H', 'PT');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function subHour($val = 1)
    {
        $val and $this->_changeDT('sub', $val, 'H', 'PT');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function addDay($val = 1)
    {
        $val and $this->_changeDT('add', $val, 'D', 'P');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function subDay($val = 1)
    {
        $val and $this->_changeDT('sub', $val, 'D', 'P');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function addMonth($val = 1)
    {
        $val and $this->_changeDT('add', $val, 'M', 'P');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function subMonth($val = 1)
    {
        $val and $this->_changeDT('sub', $val, 'M', 'P');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function addYear($val = 1)
    {
        $val and $this->_changeDT('add', $val, 'Y', 'P');
        return $this;
    }

    /**
     * @param int $val
     * @return $this
     * @throws \Exception
     */
    public function subYear($val = 1)
    {
        $val and $this->_changeDT('sub', $val, 'Y', 'P');
        return $this;
    }

    /**
     * @param string $format
     * @return string
     */
    public function format($format = 'Y-m-d H:i:s')
    {
        return $this->dt->format($format);
    }

    /**
     * @param string $format
     * @param string|int|bool $timestamp
     * @param bool $isTimestamp
     * @return string
     */
    public static function quickFormat(string $format, $timestamp = false, $isTimestamp = false)
    {
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

        return date($format, $timestamp) ?: '';
    }


    /**
     * @param $val
     * @param string $unit
     * @param \DateTime|null $dt
     * @return mixed
     */
    public static function quickChange($val, $unit = 'second', \DateTime $dt = null)
    {
        $obj = new self($dt);
        $func = 'add'.ucfirst($unit);
        return $obj->$func($val)->format();
    }

}