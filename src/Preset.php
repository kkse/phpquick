<?php
namespace kkse\quick;


/**
 * 预定义处理类
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/2/12
 * Time: 16:45
 */
final class Preset extends lang\Preset
{
    /**
     * 预定义的函数别名处理列表
     * @var array
     */
    public static $func_map = [
        'function_alias' => [lang\Func::class, 'functionAlias'],
        'uintval' => [lang\Val::class, 'uintval'],
        'bintval' => [lang\Val::class, 'bintval'],
        'to_yuan_price' => [lang\Val::class, 'toYuanPrice'],
        'to_fen_price' => [lang\Val::class, 'toFenPrice'],
    ];
}