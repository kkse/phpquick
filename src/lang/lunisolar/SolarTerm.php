<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/9/8
 * Time: 11:05
 */

namespace kkse\quick\lang\lunisolar;

/**
 * 节气定义类
 * Class SolarTerm
 * @package kkse\quick\lang\lunisolar
 */
final class SolarTerm
{
    const TYPE_NAMES = [
        "立春", "雨水", "惊蛰", "春分", "清明", "谷雨",
        "立夏", "小满", "芒种", "夏至", "小暑", "大暑",
        "立秋", "处暑", "白露", "秋分", "寒露", "霜降",
        "立冬", "小雪", "大雪", "冬至", "小寒", "大寒",
    ];
    const TYPE_START_OF_SPRING = 0;//立春
    const TYPE_THE_RAINS = 1;//雨水
    const TYPE_AWAKENING_OF_INSECTS = 2;//惊蛰
    const TYPE_VERNAL_EQUINOX = 3;//春分
    const TYPE_TOMB_SWEEPING = 4;//清明
    const TYPE_GRAIN_RAIN = 5;//谷雨
    const TYPE_LI_XIA = 6;//立夏
    const TYPE_GRAIN_BUDS = 7;//小满
    const TYPE_GRAIN_IN_EAR = 8;//芒种
    const TYPE_SUMMER_SOLSTICE = 9;//夏至
    const TYPE_SLIGHT_HEAT = 10;//小暑
    const TYPE_GREAT_HEAT = 11;//大暑
    const TYPE_BEGINNING_OF_AUTUMN = 12;//立秋
    const TYPE_THE_LIMIT_OF_HEAT = 13;//处暑
    const TYPE_WHITE_DEW = 14;//白露
    const TYPE_AUTUMNAL_EQUINOX = 15;//秋分
    const TYPE_COLD_DEW = 16;//寒露
    const TYPE_FIRST_FROST = 17;//霜降
    const TYPE_BEGINNING_OF_WINTER = 18;//立冬
    const TYPE_LIGHT_SNOW = 19;//小雪
    const TYPE_MAJOR_SNOW = 20;//大雪
    const TYPE_WINTER_SOLSTICE = 21;//冬至
    const TYPE_LESSER_COLD = 22;//小寒
    const TYPE_GREAT_COLD = 23;//大寒

    protected $type = 0;

    protected static $objs = [];
    protected function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * 禁止克隆
     */
    private function __clone()
    {
    }

    public static function getObj(int $type)
    {
        $type = ($type%24+24)%24;//获取 0-23范围的值
        if (isset($objs[$type])) {
            return $objs[$type];
        }

        return $objs[$type] = new self($type);
    }

    public function __toString():string
    {
        return self::TYPE_NAMES[$this->type];
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
}