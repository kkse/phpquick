<?php
namespace kkse\quick\lang;


/**
 * 预定义处理类
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/2/12
 * Time: 16:45
 */
class Preset
{
    /**
     * 预定义的函数别名处理列表
     * @var array
     */
    public static $func_map = [];

    /**
     * 常量定义
     * @var array
     */
    public static $constant_map = [];

    /**
     * 保存的路径
     * @var null
     */
    public static $cache_dir = null;

    /**
     * 处理预设配置
     * @param array $option
     */
    final public static function handle(array $option = [])
    {
        if (!is_null(static::$cache_dir)) {
            $postfix = $option?serialize($option):'';
            $file_name = md5(static::class.$postfix).'.php';
            $file = new Storage(static::$cache_dir.'/'.$file_name);
            //使用缓存文件加快响应
            if (!$file->isFile()) {
                $content = '<?php'.PHP_EOL;
                $content .= Func::batchAlias(static::$func_map,true).PHP_EOL;//批量别名函数
                $content .= Constant::batchDefine(static::$constant_map,true).PHP_EOL;
                $content .= static::doOption($option, true).PHP_EOL;
                $file->putContents($content, LOCK_EX);
            }
            $file->include(true);
        } else {
            Func::batchAlias(static::$func_map);//批量别名函数
            Constant::batchDefine(static::$constant_map);
            static::doOption($option);
        }


    }

    protected static function doOption(array $option = [], $isReturn = false)
    {
        return '';
    }
}