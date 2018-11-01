<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/7/2
 * Time: 15:58
 */

namespace kkse\quick\lang;

/**
 * 模拟jQuery.callbacks(flags)
 * Class Callbacks
 * @package kkse\quick\lang
 */
class Callbacks
{
    const FLAGS_ONCE = 1;//once: 确保这个回调列表只执行一次(像一个递延 Deferred).
    const FLAGS_MEMORY = 2;//memory: 保持以前的值和将添加到这个列表的后面的最新的值立即执行调用任何回调 (像一个递延 Deferred).
    const FLAGS_UNIQUE = 4;//unique: 确保一次只能添加一个回调(所以有没有在列表中的重复).
    const FLAGS_STOP_ON_FALSE = 8;//stopOnFalse: 当一个回调返回false 时中断调用

    protected $disable = false;
    protected $is_lock = false;
    protected $flags = 0;
    protected $calls_hash = [];
    protected $calls = [];//回调的列表
    protected $called_history_parameter = [];//曾经回调的历史列表参数
    protected $called_history_result = [];//曾经回调的历史列表最后的返回结果
    /**
     * Callbacks constructor.
     * @param int $flags
     */
    public function __construct(int $flags = 0)
    {
        $this->flags = $flags;
    }

    public function add(\Closure ...$calls) {
        if ($this->disable || $this->is_lock) {
            return;
        }

        foreach ($calls as $call) {
            if ($this->flags&self::FLAGS_UNIQUE) {
                if ($this->has($call)) {
                    continue;
                }
            }

            $this->calls_hash[] = spl_object_hash($call);
            $this->calls[] = $call;

            if ($this->flags&self::FLAGS_MEMORY) {
                $stop_on_false = $this->flags&self::FLAGS_STOP_ON_FALSE;
                foreach ($this->called_history_result as $key => $result) {
                    if ($stop_on_false && $result === false) {
                        continue;
                    }
                    $this->called_history_result[$key] = call_user_func_array($call, $this->called_history_parameter[$key]);
                }
            }
        }
    }

    public function has(\Closure $call)
    {
        return in_array(spl_object_hash($call),$this->calls_hash);
    }

    public function disable()
    {
        $this->disable = true;
    }

    public function lock()
    {
        $this->is_lock = true;
    }

    public function locked()
    {
        return $this->is_lock;
    }

    public function empty()
    {
        $this->calls_hash = [];
        $this->calls = [];
        $this->called_history_parameter = [];
        $this->called_history_result = [];
        $this->disable = false;
        $this->is_lock = false;
    }

    public function fire(...$arguments) {
        if ($this->disable || $this->is_lock) {
            return false;
        }

        if ($this->flags&self::FLAGS_ONCE  &&  $this->fired()) {
            return false;
        }

        $key = count($this->called_history_parameter);
        $this->called_history_parameter[$key] = $arguments;
        $this->called_history_result[$key] = null;

        $stop_on_false = $this->flags&self::FLAGS_STOP_ON_FALSE;
        foreach ($this->calls as $call) {
            if ($stop_on_false && $this->called_history_result[$key] === false) {
                break;
            }
            $this->called_history_result[$key] = call_user_func_array($call, $arguments);
        }
        return $this->called_history_result[$key];
    }
    public function fired() {
        return !empty($this->called_history_result);
    }

}