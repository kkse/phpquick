<?php

namespace kkse\quick\lang\lunisolar;

/**
 * 阴阳历
 * Class Date
 * @package kkse\quick\lang
 */
class Date
{
    protected static $month_names = ["正月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "冬月", "腊月"];
    protected static $day_names = ["初一", "初二", "初三", "初四", "初五", "初六", "初七", "初八", "初九", "初十", "十一", "十二", "十三", "十四", "十五", "十六", "十七", "十八", "十九", "二十", "廿一", "廿二", "廿三", "廿四", "廿五", "廿六", "廿七", "廿八", "廿九", "三十"];
    protected static $shu_jiu_names = ["一九", "二九", "三九", "四九", "五九", "六九", "七九", "八九", "九九"];

    protected static $heavenly_stem_names = ["甲", "乙", "丙", "丁", "戊", "己", "庚", "辛", "壬", "癸"];
    protected static $earthly_names = ["子", "丑", "寅", "卯", "辰", "巳", "午", "未", "申", "酉", "戌", "亥"];
    protected static $symbolic_animals_names = ["鼠", "牛", "虎", "兔", "龙", "蛇", "马", "羊", "猴", "鸡", "狗", "猪"];
    protected static $five_element_names = [
        '金鼠','金牛','火虎','火兔','木龙', '木蛇',
        '土马','土羊','金猴','金鸡','火狗', '火猪',
        '水鼠','水牛','土虎','土兔','金龙', '金蛇',
        '木马','木羊','水猴','水鸡','土狗', '土猪',
        '火鼠','火牛','木虎','木兔','水龙', '水蛇',
        '金马','金羊','火猴','火鸡','木狗', '木猪',
        '土鼠','土牛','金虎','金兔','火龙', '火蛇',
        '水马','水羊','土猴','土鸡','金狗', '金猪',
        '木鼠','木牛','水虎','水兔','土龙', '土蛇',
        '火马','火羊','木猴','木鸡','水狗', '水猪',
    ];


    protected static $START_YEAR = 1600;//定义数据起始年份（公历）
    protected static $END_YEAR = 6400;//定义数据终止年份（不包含该年）

    //起始年的上一年农历十月及以后的闰月索引，对应cPreMonth中的序号，当前为-1，表示农历十月以后无闰月。
    protected static $cPreLeapIndex = -1;//可能值：-1:无闰月,0:11月闰月/12月闰月

    //起始年的上一年农历十月及以后的月份，每月初一在起始年的序数。
    protected static $cPreMonth = [-44, -15, 15, 44];//当存在闰11、12月时，会有5个值

    //农历月份信息。一年用3个字节表示
    //+-----------------------------------------------------------------------+
    //| 第23位 |        第22-17位       |  第16-13位 |       第12-0位         |
    //|--------+------------------------+------------+------------------------|
    //|  保留  | 农历正月初一的年内序数 |    闰月    | 一个位对应一个月份大小 |
    //+-----------------------------------------------------------------------+
    //月份大小数据是月份小的在低位，月份大的在高位，即正月在最低位。
    //以1900年为例，3个字节的数据展开成二进制位：
    //  0       011110        1000                     1 0 1 1 0 1 1 0 1 0 0 1 0
    //保留  1月31日（春节）  闰八月   从左往右依次十二月，十一月……闰八月、八月、七月……正月的天数
    //农历月份对应的位为0，表示这个月为29天（小月），为1表示有30天（大月）。
    protected static $cMonthInfo = [];


    //二十四节气信息。一年用6个字节表示，每个节气使用两位数据。
    //+-------------------------------------------------------+
    //| 第一字节最高两位 |  第一字节其余6位至第六字节共46个位 |
    //|------------------+------------------------------------|
    //|小寒的年内序数减3 | 每个节气距离上一节气的天数，共23组 |
    //+-------------------------------------------------------+
    //小寒的年内序数已给出，剩下的23个节气分别对应这23组数据，有以下含义：
    //+-------------------------------------------------------+
    //|  二进制位 | 意义 |                描述                |
    //|-----------+------+------------------------------------|
    //|     00    | 14天 |  当前对应的节气距离上一节气为14天  |
    //|-----------+------+------------------------------------|
    //|     01    | 15天 |  当前对应的节气距离上一节气为15天  |
    //|-----------+------+------------------------------------|
    //|     10    | 16天 |  当前对应的节气距离上一节气为16天  |
    //|-----------+------+------------------------------------|
    //|     11    | 17天 |  当前对应的节气距离上一节气为17天  |
    //+-------------------------------------------------------+
    //节气顺序：
    //小寒 大寒 立春 雨水 惊蛰 春分 清明 谷雨 立夏 小满 芒种 夏至
    //小暑 大暑 立秋 处暑 白露 秋分 寒露 霜降 立冬 小雪 大雪 冬至
    protected static $cSolarTerms = [];

    //1599年冬至日在1600年的年内序数，这个数据将被用在1600年“数九”的计算上。
    protected static $cPreDongzhiOrder = -10;

    //每年数九、入梅、出梅及三伏信息，一年用两个字节表示。
    //+---------------------------------------------------+
    //|  第15-11位 |  第10-6位  |  第5-1位   |   最末位   |
    //|------------+------------+------------+------------|
    //|    入梅    |    出梅    |    初伏    |    末伏    |
    //+---------------------------------------------------+
    //1.“一九”即是冬至，往后到“九九”的每个“九”相差9天，可顺利推算出来，故“数九”信息省略。
    //2.“三伏”天的“中伏”在“初伏”后10天，而“末伏”在“中伏”后10天或20天，因此“中伏”信息省略。
    //入梅信息：该天数加上150为入梅这一日的年内序数。
    //出梅信息：该天数加上180为出梅这一日的年内序数。
    //初伏信息：该天数加上180为初伏这一日的年内序数。
    //末伏信息：若该位为“1”，表示末伏距离初伏30天，为“0”表示末伏距离初伏20天。
    protected static $wExtermSeason = [];

    protected $year;
    protected $month;
    protected $day;
    protected $isLeapMonth;

    protected $cd = [];//缓存数据

    /**
     * Date constructor.
     * @param int $year
     * @param int $month
     * @param int $day
     * @param bool $isLeapMonth
     */
    public function __construct(int $year, int $month, int $day, bool $isLeapMonth = false)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->isLeapMonth = $isLeapMonth;
        $this->_dateCheck();//检查日期格式是否正确
    }


    /**
     * 根据公历转为阴阳历
     * @param \DateTime $datetime
     * @return Date
     */
    public static function createFromDateTime(\DateTime $datetime)
    {
        $year = intval($datetime->format('Y'));

        if ($year < self::$START_YEAR || $year >= self::$END_YEAR) {
            throw new \RuntimeException('该日期年份不支持');
        }
        //$month = intval($datetime->format('m'));
        //$day = intval($datetime->format('d'));
        $daysNum = intval($datetime->format('z'));//今年的第几天

        $obj = self::createFromYearDays($year, $daysNum);
        $obj->cd['datetime'] = $datetime->format('Y-m-d');
        return $obj;
    }

    /**
     * @param int $year 农历年
     * @param SolarTerm|int $st 节气
     * @return Date
     */
    public static function createFromSolarTerm(int $year, $st)
    {
        is_int($st) and $st = SolarTerm::getObj($st);
        if (!($st instanceof SolarTerm)) {
            throw new \RuntimeException('需要提供节气');
        }

        if ($st->getType() >= SolarTerm::TYPE_LESSER_COLD) {//小寒、大寒
            $year += 1;
        }

        $infos = self::getSolarTermInfo($year);

        $pos = ($st->getType() - SolarTerm::TYPE_LESSER_COLD + 24) % 24;

        $date = new \DateTime();
        $date->setTime(0, 0, 0);
        $date->setDate($year, 1, 1);
        $date->add(new \DateInterval("P{$infos[$pos]}D"));

        return self::createFromDateTime($date);
    }

    /**
     * @param int $year 公历年
     * @param int $days 第几天
     * @return Date
     */
    public static function createFromYearDays(int $year, int $days)
    {
        $lYear = $year;
        $daysNum = $days;

        $isLeapMonth = false;

        //农历新年的公历年内序数
        $lunisolarNewYear = self::LunarGetNewYearOrdinal($year);

        //获取月份天数，数组从上一年十一月开始到今年（闰）十二月，包含闰月
        $DaysOfLunarMonth = self::LunarExpandDX($lYear);//存放农历月份天数

        $iLeapMonthPre = self::LunarGetLeapMonth($lYear - 1);//农历上一年闰月
        $iLeapMonth = $iLeapMonthPre ? 0 : self::LunarGetLeapMonth($lYear);//上一年没有闰月，则查询今年闰月

        $remain_days = $daysNum - $lunisolarNewYear;//距离农历新年天数
        if ($iLeapMonthPre > 10) {
            $i = 3;//今年正月在“DaysOfLunarMonth”中的索引，上一年十一月或十二月有闰月
        } else {
            $i = 2;//上一年十一月和十二月无闰月时，今年正月的索引
        }

        if ($lunisolarNewYear > $daysNum)//早于农历新年
        {
            $lYear -= 1;//农历年减1
            while ($remain_days < 0) {
                $i--;//第一次先减去是因为当前i是正月，减1表示上一年十二月（或闰十二月）
                $remain_days += $DaysOfLunarMonth[$i];//加上上一年十二月、十一月的总天数（含闰月）直到日数大于0
            }
            if ($iLeapMonthPre > 10)//如果上一年十一月或十二月存在闰月
            {
                if ($iLeapMonthPre == 11)//闰十一月
                {
                    if ($i == 0)//十一月（即在闰月之前）
                    {
                        $lMonth = 11 + $i;//转换到月份
                    } else {
                        $lMonth = 10 + $i;
                        if ($lMonth == $iLeapMonthPre) {
                            $isLeapMonth = true;
                        }
                    }
                } else if ($iLeapMonthPre == 12)//闰十二月
                {
                    if ($i < 2)//在闰月之前
                    {
                        $lMonth = 11 + $i;
                    } else {
                        $lMonth = 10 + $i;
                        if ($lMonth == $iLeapMonthPre) {
                            $isLeapMonth = true;
                        }
                    }
                } else {
                    throw new \RuntimeException('逻辑异常');
                }
            } else {
                $lMonth = 11 + $i;
            }
            $lDay = $remain_days;
        } else {
            while ($remain_days >= $DaysOfLunarMonth[$i]) {
                $remain_days -= $DaysOfLunarMonth[$i];//寻找农历月
                $i++;//移至下个月
            }
            if ($iLeapMonthPre > 10) {
                $lMonth = $i - 2;
            } else {
                if ($iLeapMonth > 0) {
                    if ($i - 2 < $iLeapMonth) {
                        $lMonth = $i - 1;
                    } else {
                        $lMonth = $i - 2;
                        if ($lMonth == $iLeapMonth) {
                            $isLeapMonth = true;
                        }
                    }
                } else {
                    $lMonth = $i - 1;
                }
            }
            $lDay = $remain_days;
        }
        $lDay += 1;//索引转换到数量

        $obj = new self($lYear, $lMonth, $lDay, $isLeapMonth);
        return $obj;
    }

    /**
     * @param int $year 公历年
     * @return array [Date, Date]
     */
    public static function getRuChuMei(int $year)
    {
        if ($year < self::$START_YEAR || $year >= self::$END_YEAR) {
            throw new \RuntimeException('该日期年份不支持');
        }

        $et_index = $year - self::$START_YEAR;


        $wRuMeiOrd = ((self::$wExtermSeason[$et_index] & 0xF800) >> 11) + 150;
        $wChuMeiOrd = ((self::$wExtermSeason[$et_index] & 0x07C0) >> 6) + 180;

        return [self::createFromYearDays($year, $wRuMeiOrd), self::createFromYearDays($year, $wChuMeiOrd)];
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     */
    public static function GetDayOrdinal(int $year, int $month, int $day)
    {
        $dt = new \DateTime();
        $dt->setDate($year, $month, $day);
        return intval($dt->format('z'));
    }

    /**
     * 返回公历
     */
    public function toDateTime()
    {
        if (isset($this->cd['datetime'])) {
            return new \DateTime($this->cd['datetime']);
        }

        if ($this->year < self::$START_YEAR) {//在开始日期前
            //获取月份天数，数组从上一年十一月开始到今年（闰）十二月，包含闰月
            $DaysOfLunarMonth = self::LunarExpandDX($this->year + 1);//有可能是下一年的
            $LunarNewYear = self::LunarGetNewYearOrdinal($this->year + 1);//找到正月初一r公历年内序数
            $iLeapMonthPre = self::LunarGetLeapMonth($this->year);

            $remain_days = 0;
            //$remain_days = $LunarNewYear;//加上正月初一的序数

            if ($iLeapMonthPre > 10) {//存在闰月
                $i = 2;//十二月

                if ($this->isLeapMonth) {//是闰月
                    if ($iLeapMonthPre == 12) {
                        $endi = 2;
                    } else {
                        $endi = 1;
                    }
                } else {
                    if ($iLeapMonthPre == 12) {
                        if ($this->month == 12) {
                            $endi = 1;
                        } else {
                            $endi = 0;
                        }
                    } else {
                        if ($this->month == 12) {
                            $endi = 2;
                        } else {
                            $endi = 0;
                        }
                    }
                }
            } else {
                $i = 1;//十二月
                if ($this->month == 12) {
                    $endi = 1;
                } else {
                    $endi = 0;
                }
            }

            for (; $i > $endi; $i--) {
                $remain_days += $DaysOfLunarMonth[$i];//年内序数累加
            }

            $remain_days += $DaysOfLunarMonth[$endi] - $this->day;

            $remain_days = $LunarNewYear - $remain_days - 1;

            $dt = new \DateTime();
            $dt->setTime(0, 0, 0);
            $dt->setDate($this->year + 1, 1, 1);
            $dt->add(new \DateInterval("P{$remain_days}D"));
            $this->cd['datetime'] = $dt->format('Y-m-d');
            return $dt;
        } else {
            $DaysOfLunarMonth = self::LunarExpandDX($this->year);//有可能是下一年的
            $LunarNewYear = self::LunarGetNewYearOrdinal($this->year);//找到正月初一r公历年内序数
            $iLeapMonthPre = self::LunarGetLeapMonth($this->year - 1);
            $iLeapMonth = $iLeapMonthPre ? 0 : self::LunarGetLeapMonth($this->year);//找出农历年的闰月

            $remain_days = $LunarNewYear;//加上正月初一的序数
            if ($iLeapMonthPre > 10) {
                $i = 3;//今年正月在“DaysOfLunarMonth”中的索引，上一年十一月或十二月有闰月
            } else {
                $i = 2;//上一年十一月和十二月无闰月时，今年正月的索引
            }
            if ($iLeapMonth == 0) {
                if ($iLeapMonthPre > 10) {
                    for (; $i < $this->month + 2; $i++) {
                        $remain_days += $DaysOfLunarMonth[$i];//年内序数累加
                    }
                } else {
                    for (; $i < $this->month + 1; $i++) {
                        $remain_days += $DaysOfLunarMonth[$i];//年内序数累加
                    }
                }
            } else {
                if ($this->month < $iLeapMonth || (!$this->isLeapMonth && $this->month == $iLeapMonth))//在闰月之前
                {
                    for (; $i < $this->month + 1; $i++) {
                        $remain_days += $DaysOfLunarMonth[$i];
                    }
                } else {
                    for (; $i < $this->month + 2; $i++) {
                        $remain_days += $DaysOfLunarMonth[$i];
                    }
                }
            }
            $remain_days += $this->day - 1;//减1是因为日名转到序数

            $dt = new \DateTime();
            $dt->setTime(0, 0, 0);
            $dt->setDate($this->year, 1, 1);
            $dt->add(new \DateInterval("P{$remain_days}D"));
            $this->cd['datetime'] = $dt->format('Y-m-d');
            return $dt;
        }
    }

    /**
     * 检查日期数据是否正确
     * @return bool
     */
    private function _dateCheck()
    {
        if ($this->year < self::$START_YEAR - 1 || $this->year >= self::$END_YEAR
            || $this->month < 1 || $this->month > 12
            || $this->day < 1 || $this->day > 30
        ) {
            //年、月、日检查
            throw new \RuntimeException('日期数据不正确');
        }

        if ($this->year == self::$START_YEAR - 1 && $this->month < 11) {
            throw new \RuntimeException('日期数据不正确');
        }

        if ($this->isLeapMonth && self::LunarGetLeapMonth($this->year) != $this->month) {
            throw new \RuntimeException('闰月不一致');
        }

        return true;
    }

    /**
     * 通过年份，获取当年的农历正月初一的年内序数
     * @param int $year
     * @return int
     */
    public static function LunarGetNewYearOrdinal(int $year)
    {
        $uData = self::$cMonthInfo[$year - self::$START_YEAR];
        return ($uData >> 17) & 0b111111;//取出农历新年的年内序数,6位
    }

    /**
     * 通过年份，获取当年的闰月月份
     * @param int $year
     * @return int
     */
    public static function LunarGetLeapMonth(int $year)
    {
        if ($year == self::$START_YEAR - 1 && self::$cPreLeapIndex != -1) {
            return self::$cPreLeapIndex + 9;
        }

        if ($year >= self::$START_YEAR) {
            $uData = self::$cMonthInfo[$year - self::$START_YEAR];
            return ($uData >> 13) & 0b1111;//4位
        }
        return 0;
    }

    /**
     * 获取指定年月的天数
     * @param int $lyear
     * @param int $lmonth
     * @param bool $isLeapMonth
     * @return int
     */
    protected static function LunarGetDaysofMonth(int $lyear, int $lmonth, bool $isLeapMonth = false)
    {
        if ($lyear == self::$START_YEAR - 1) {
            //无闰月
            if (self::$cPreLeapIndex == -1) {
                if ($isLeapMonth) {
                    return 0;
                }
                return self::$cPreMonth[$lmonth - 9] - self::$cPreMonth[$lmonth - 10];
            } else {
                $Acc_LeapMonth = self::$cPreLeapIndex + 9;
                if ($Acc_LeapMonth > $lmonth) {
                    if ($isLeapMonth) {
                        return 0;
                    }
                    return self::$cPreMonth[$lmonth - 9] - self::$cPreMonth[$lmonth - 10];
                } else {
                    if (($isLeapMonth && $lmonth == $Acc_LeapMonth) || $lmonth > $Acc_LeapMonth) {
                        return self::$cPreMonth[$lmonth - 8] - self::$cPreMonth[$lmonth - 9];
                    } else {
                        return self::$cPreMonth[$lmonth - 9] - self::$cPreMonth[$lmonth - 10];
                    }
                }
            }
        }

        $uData = self::$cMonthInfo[$lyear - self::$START_YEAR];
        $Acc_LeapMonth = self::LunarGetLeapMonth($lyear);//获取真实闰月月份

        $dx_data = $uData & 0x1FFF;//整年大小月情况

        if ($isLeapMonth)//指定查询的当前月是闰月
        {
            if ($Acc_LeapMonth != $lmonth) {
                return 0;//不存在的闰月
            }

            for ($i = 0; $i < $lmonth; $i++) {
                $dx_data >>= 1;//右移一位，即从末尾开始找该闰月月份所在的位
            }
        } else {
            if ($Acc_LeapMonth > 0) {//存在闰月
                if ($lmonth <= $Acc_LeapMonth)//月份在闰月之前，倒找需要多找一位
                {
                    for ($i = 0; $i < $lmonth - 1; $i++) {
                        $dx_data >>= 1;
                    }
                } else {
                    for ($i = 0; $i < $lmonth; $i++) {
                        $dx_data >>= 1;
                    }
                }
            } else {
                for ($i = 0; $i < $lmonth - 1; $i++) {
                    $dx_data >>= 1;
                }
            }
        }

        if ($dx_data & 0x1) {
            return 30;//大月
        } else {
            return 29;//小月
        }
    }


    private static function LunarExpandDX(int $lYear)
    {
        $iDayOfMonth = [];
        if ($lYear == self::$START_YEAR) {
            if (self::$cPreLeapIndex == -1)//处理起始年份之前一年数据
            {
                $iDayOfMonth[] = self::$cPreMonth[2] - self::$cPreMonth[1];//农历上一年十一月总天数
                $dec_i = 2;
            } else {
                //闰十一月或闰十二月
                $iDayOfMonth[] = self::$cPreMonth[2] - self::$cPreMonth[1];
                $iDayOfMonth[] = self::$cPreMonth[3] - self::$cPreMonth[2];
                $dec_i = 3;
            }
            $iDayOfMonth[] = self::LunarGetNewYearOrdinal($lYear) - self::$cPreMonth[$dec_i];
        } else {
            $iLeapMonth = self::LunarGetLeapMonth($lYear - 1);//取得前一农历年的闰月情况
            if ($iLeapMonth < 11)//一月至十月的闰月
            {
                $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear - 1, 11, false);//取上一年十一月天数
                $iDayOfMonth[] = self:: LunarGetDaysofMonth($lYear - 1, 12, false);//取上一年十二月天数
            } else {
                $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear - 1, 11, false);//取上一年十一月的天数
                if ($iLeapMonth == 11) {//闰十一月
                    $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear - 1, 11, true);//取上一年闰十一月的天数
                    $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear - 1, 12, false);//取上一年十二月天数
                } else if ($iLeapMonth == 12) {
                    $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear - 1, 12, false);//取上一年十二月天数
                    $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear - 1, 12, true);//取上一年闰十二月天数
                }
            }
        }

        $iLeapMonth = self::LunarGetLeapMonth($lYear);//获取当前农历年的闰月情况
        for ($i = 0; $i < 12; $i++) {
            $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear, $i + 1, false);//取每个农历月天数
            if ($iLeapMonth > 0 && $iLeapMonth == $i + 1) {
                $iDayOfMonth[] = self::LunarGetDaysofMonth($lYear, $i + 1, true);//取闰月的天数
            }
        }

        return $iDayOfMonth;
    }


    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    public function getMonthName()
    {
        $month_name = self::$month_names[$this->month - 1];
        $this->isLeapMonth and $month_name = '闰' . $month_name;
        return $month_name;
    }

    /**
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }

    public function getDayName()
    {
        $day_name = self::$day_names[$this->day - 1];
        return $day_name;
    }

    /**
     * @return bool
     */
    public function isLeapMonth(): bool
    {
        return $this->isLeapMonth;
    }


    /**
     * @param bool $isGre 是否公历年
     * @return int
     */
    public function getYear($isGre = false): int
    {
        if ($isGre) {
            return intval($this->toDateTime()->format('Y'));
        } else {
            return $this->year;
        }
    }


    public function getYearDays()
    {
        $dt = $this->toDateTime();
        return intval($dt->format('z'));
    }


    public function __toString(): string
    {
        return sprintf('农历%s年%s%s', $this->year, $this->getMonthName(), $this->getDayName());
    }


    private static function unpackST(int $st_int)
    {
        $infos = [];

        for ($i = 0; $i < 12; $i++) {
            $infos[] = $st_int & 0x03;
            $st_int = $st_int >> 2;
        }

        return array_reverse($infos);
    }

    public static function getSolarTermInfo(int $year)
    {
        if ($year < self::$START_YEAR || $year >= self::$END_YEAR) {
            throw new \RuntimeException('该日期年份不支持');
        }

        $pos = ($year - self::$START_YEAR) * 2;
        $infos = array_merge(self::unpackST(self::$cSolarTerms[$pos]), self::unpackST(self::$cSolarTerms[$pos + 1]));

        $infos[0] += 3;
        for ($i = 0; $i < 23; $i++) {
            $infos[$i + 1] = $infos[$i] + 14 + $infos[$i + 1];
        }

        return $infos;
    }

    /**
     * 获取所在节气
     * @param bool $is_first 为true时，只有第一天才会返回
     * @return SolarTerm|null
     */
    public function getSolarTerm($is_first = false)
    {
        $dt = $this->toDateTime();//需要先转为公历
        $days = intval($dt->format('z'));
        $infos = self::getSolarTermInfo(intval($dt->format('Y')));

        if ($is_first && !in_array($days, $infos)) {
            return null;
        }

        $pos = -1;
        foreach ($infos as $i => $st_day) {
            $pos = $i;
            if ($days < $st_day) {
                $pos = $i - 1;
                break;
            }
        }

        return SolarTerm::getObj($pos + SolarTerm::TYPE_LESSER_COLD);
    }

    /**
     * 获取数九信息
     * @param bool $is_name 是否返回数九名称
     * @return bool|int  false 表示不属于数九，0-8 表示数九索引, 或者数九名称
     */
    public function getTheBeginningOfWinterFrost(bool $is_name = false)
    {
        if ($this->year < self::$START_YEAR) {//拿不到信息
            return false;
        }

        //获取今年的冬至
        $they = self::createFromSolarTerm($this->year, SolarTerm::TYPE_WINTER_SOLSTICE);
        $cmp = $this->cmp($they);
        if ($cmp >= 0) {
            $daynum = $this->diffDay($they);
            if ($daynum > 80) {
                return false;
            }
            $shujiu = intval($daynum / 9);
            return $is_name ? self::$shu_jiu_names[$shujiu] : $shujiu;
        }

        if ($this->year == self::$START_YEAR) {
            return false;
        }

        $they = self::createFromSolarTerm($this->year - 1, SolarTerm::TYPE_WINTER_SOLSTICE);
        $cmp = $this->cmp($they);
        if ($cmp >= 0) {
            $daynum = $this->diffDay($they);
            if ($daynum > 80) {
                return false;
            }
            $shujiu = intval($daynum / 9);
            return $is_name ? self::$shu_jiu_names[$shujiu] : $shujiu;
        }
        return false;
    }

    /**
     * 是否数九的第一天
     * @return bool
     */
    public function isTheBeginningOfWinterFrost()
    {
        if ($this->year < self::$START_YEAR) {//拿不到信息
            return false;
        }

        //获取今年的冬至
        $they = self::createFromSolarTerm($this->year, SolarTerm::TYPE_WINTER_SOLSTICE);
        $cmp = $this->cmp($they);
        if ($cmp >= 0) {
            $daynum = $this->diffDay($they);
            if ($daynum > 80) {
                return false;
            }
            return $daynum % 9 == 0;
        }

        if ($this->year == self::$START_YEAR) {
            return false;
        }

        $they = self::createFromSolarTerm($this->year - 1, SolarTerm::TYPE_WINTER_SOLSTICE);
        $cmp = $this->cmp($they);
        if ($cmp >= 0) {
            $daynum = $this->diffDay($they);
            if ($daynum > 80) {
                return false;
            }
            return $daynum % 9 == 0;
        }
        return false;
    }


    /**
     * 是否入梅
     * @return bool
     */
    public function isRuMei()
    {
        $list = self::getRuChuMei($this->getYear(true));
        return $this->cmp($list[0]) == 0;
    }

    /**
     * 是否出梅
     * @return bool
     */
    public function isChuMei()
    {
        $list = self::getRuChuMei($this->getYear(true));
        return $this->cmp($list[1]) == 0;
    }

    /**
     * 是否在梅雨中
     * @return bool
     */
    public function inMei()
    {
        $list = self::getRuChuMei($this->getYear(true));

        return $this->cmp($list[0]) >= 0 && $this->cmp($list[1]) <= 0;
    }



    /**
     * 是否初伏
     * @param bool $is_first
     * @return bool
     */
    public function isChuFu($is_first = false){
        $et_index = $this->getYear(true) - self::$START_YEAR;
        $wChuFu = ((self::$wExtermSeason[$et_index]&0x3E)>>1)+180;
        $wZhongfu = $wChuFu+10;
        if ($is_first) {
            return $this->getYearDays() == $wChuFu;
        } else {
            $year_days = $this->getYearDays();
            return  $year_days >= $wChuFu && $year_days<$wZhongfu;
        }
    }

    /**
     * 是否中伏
     * @param bool $is_first
     * @return bool
     */
    public function isZhongFu($is_first = false){
        $et_index = $this->getYear(true) - self::$START_YEAR;
        $wChuFu=((self::$wExtermSeason[$et_index]&0x3E)>>1)+180;
        $wZhongfu = $wChuFu+10;
        $wMoFu = $wChuFu+((self::$wExtermSeason[$et_index]&0x01)==1?30:20);
        if ($is_first) {
            return $this->getYearDays() == $wZhongfu;
        } else {
            $year_days = $this->getYearDays();
            return  $year_days >= $wZhongfu && $year_days<$wMoFu;
        }
    }

    /**
     * 是否末伏
     * @param bool $is_first
     * @return bool
     */
    public function isMoFu($is_first = false){
        $et_index = $this->getYear(true) - self::$START_YEAR;
        $wChuFu=((self::$wExtermSeason[$et_index]&0x3E)>>1)+180;
        $wMoFu = $wChuFu+((self::$wExtermSeason[$et_index]&0x01)==1?30:20);
        if ($is_first) {
            return $this->getYearDays() == $wMoFu;
        } else {
            $year_days = $this->getYearDays();
            return  $year_days >= $wMoFu && $year_days<$wMoFu+10;
        }
    }

    public function getHeavenlyStem(bool $is_name = false){
        $pos = ($this->year-4)%10;
        return $is_name?self::$heavenly_stem_names[$pos]:$pos;
    }
    public function getEarthly(bool $is_name = false)
    {
        $pos = ($this->year-4)%12;
        return $is_name?self::$earthly_names[$pos]:$pos;
    }

    public function getSymbolicAnimals($is_five_element = false)
    {
        if ($is_five_element) {
            $pos = ($this->year-4)%60;
            return self::$five_element_names[$pos];
        } else {
            $pos = ($this->year-4)%12;
            return self::$symbolic_animals_names[$pos];
        }
    }

    /**
     * 时间比较
     * @param Date $date
     * @return int
     */
    public function cmp(Date $date)
    {
        return strcmp($this->toDateTime()->format('Y-m-d'), $date->toDateTime()->format('Y-m-d'));
    }

    public function add(\DateInterval $interval)
    {
        return self::createFromDateTime($this->toDateTime()->add($interval));
    }

    /**
     * 返回两个时间的相差日
     * @param Date $date
     * @return int
     */
    public function diffDay(Date $date)
    {
        $this_date = $this->toDateTime();
        $this_year = intval($this_date->format('Y'));
        $this_days = intval($this_date->format('z'));

        $that_date = $date->toDateTime();
        $that_year = intval($that_date->format('Y'));
        $that_days = intval($that_date->format('z'));
        //var_dump($this_year,$this_days,$that_year, $that_days);
        $days = $this_days - $that_days;
        if ($this_year != $that_year) {
            $days += ($this_year - $that_year) * 365 + self::getLeapNum($this_year) - self::getLeapNum($that_year);
        }
        return $days;
    }

    /**
     * 获取经过的闰年数
     * @param int $year
     * @return int
     */
    public static function getLeapNum(int $year)
    {
        return intval($year / 4) - intval($year / 100) + intval($year / 400);
    }



    public static function init()
    {
        static $inited = false;
        if ($inited) return;
        $inited = true;
        $data = include_once(__DIR__ . '/lunisolar.data.php');


        self::$START_YEAR = $data['START_YEAR'];
        self::$END_YEAR = $data['END_YEAR'];
        self::$cPreLeapIndex = $data['cPreLeapIndex'];
        self::$cPreMonth = $data['cPreMonth'];
        self::$cMonthInfo = $data['cMonthInfo'];
        self::$cSolarTerms = $data['cSolarTerms'];
        self::$cPreDongzhiOrder = $data['cPreDongzhiOrder'];
        self::$wExtermSeason = $data['wExtermSeason'];
    }
}

Date::init();