<?php
namespace kkse\quick\lang;

/**
 * 函数、方法 相关的处理类
 * User: kkse
 * Date: 2018/2/12
 * Time: 16:11
 */
final class Func
{
    /**
     * 禁止实例化
     * Func constructor.
     */
    private function __construct(){}

    /**
     * 为一个函数创建别名,不过不支持使用 func_get_args获取参数的方法，只要改为 ...$args 方式的函数就可以了。
     * @param mixed $function_name  支持 字符串函数、class::func、[class_name, func_name]
     * @param string $alias_name    别名的名称
     * @param bool $isReturn        是否返回格式化后的内容
     * @return bool|string
     */
    public static function functionAlias($function_name, string $alias_name, bool $isReturn = false)
    {
        if(!is_callable($function_name)) {
            return false;
        }

        //如果是返回字符串的话，就不用检查 $alias_name 是否已经是函数了
        if(!$isReturn && function_exists($alias_name)) {
            return false;
        }

        //\x7f-\xff 不要这种特殊字符
        if(!preg_match('/^\\\\?[a-zA-Z_][a-zA-Z0-9_]*(\\\\[a-zA-Z_][a-zA-Z0-9_]*)*$/', $alias_name)) {
            return false;
        }

        //:start 去掉左边的\\ 并提取命名空间
        $alias_namespace = '';
        $alias_name = ltrim($alias_name, '\\');
        if (strpos($alias_name, '\\')) {
            $alias = explode('\\', $alias_name);
            $alias_name = array_pop($alias);
            $alias_namespace = implode('\\', $alias);
        }
        //:end

        //:start 获取ReflectionFunctionAbstract对象
        if (is_string($function_name)) {
            if (strpos($function_name, '::')) {//类的方式
                $function_name = explode('::', $function_name);
            } else {
                if(!function_exists($function_name)) {
                    return false;
                }
                $rf = new \ReflectionFunction($function_name);
            }
        }
        if (is_array($function_name) && count($function_name) == 2) {
            list ($class, $func) = $function_name;
            if (!is_string($class) || !is_string($func)) {
                return false;
            }
            try {
                $rf = new \ReflectionMethod($class , $func);
                //只支持公开且静态的方法
                if (!$rf->isStatic() || !$rf->isPublic()) {
                    return false;
                }

                $function_name = $class.'::'.$func;//可要可不要，仅仅只是为了模板好看而已
            } catch (\ReflectionException $re) {
                return false;
            }
        }
        if (!isset($rf)) {
            return false;
        }
        //:end

        //:start 构造新函数模板
        //$fcall_name = "\$\xff\xef\xdf\xcf\xbf\xaf";//防止冲突
        $fproto = $alias_name.'(';
        $fcall = '(';
        $need_comma = false;
        $pnames = [];

        foreach($rf->getParameters() as $param)
        {
            if($need_comma)
            {
                $fproto .= ',';
                $fcall .= ',';
            }

            if ($param->hasType()) {
                $type = $param->getType();
                $type->isBuiltin() or $fproto .= '\\';//防止在命名空间中出错
                $fproto .= strval($type).' ';
            }


            if ($param->isPassedByReference()) {
                $fproto .= '&';
            }

            if ($param->isVariadic()) {
                $fproto .= '...';
                $fcall .= '...';
            }

            $pname = '$'.$param->getName();

            $fproto .= $pname;
            $fcall .= $pname;
            $pnames[] = $pname;

            if($param->isOptional() && $param->isDefaultValueAvailable())
            {
                $fproto .= ' = '.var_export($param->getDefaultValue(), true);
            }
            $need_comma = true;
        }
        $fproto .= ')';
        $fcall .= ')';

        /*do {
            $fcall_name = '$func'.mt_rand();
        } while (in_array($fcall_name, $pnames));//获取一个不冲突的方法名变量

        $fcallcode = $fcall_name.' = '.var_export($function_name, true).'; return '.$fcall_name;*/
        $fcallcode = 'return '.$function_name;

        //有没有命名空间都要加上命名空间
        $f = sprintf('namespace %1$s{function %2$s{%3$s;}}', $alias_namespace, $fproto, $fcallcode.$fcall);
        //:end

        if ($isReturn) {
            return $f;
        }

        eval($f);

        return function_exists($alias_namespace.'\\'.$alias_name);
    }

    /**
     * 批量处理别名
     * @param array $func_map       批量处理数组，格式：'alias'=>'func'
     * @param bool $isReturn        是否返回格式化后的内容
     * @return string
     */
    public static function batchAlias(array $func_map, bool $isReturn = false)
    {
        if ($isReturn) {
            $str = '';
            foreach ($func_map as $alias => $func) {
                $str .= self::functionAlias($func, $alias, true).PHP_EOL;
            }
            return $str;
        } else {
            foreach ($func_map as $alias => $func) {
                self::functionAlias($func, $alias, false);
            }
        }
        return '';
    }
}