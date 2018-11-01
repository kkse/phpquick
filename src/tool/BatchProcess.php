<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/7/18
 * Time: 15:06
 */

namespace kkse\quick\tool;


/**
 * 批量数据处理
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/6/2
 * Time: 18:19
 */
class BatchProcess
{
    protected $limit = 1000;//一次性处理的数量定义
    protected $dataSet = [];//等待处理的数据列表
    protected $doBatch;//批量处理数据的函数


    /**
     * BatchProcess constructor.
     * @param callable $doBatch
     * @param int $limit
     */
    public function __construct(callable $doBatch, $limit = 1000)
    {
        $this->doBatch = $doBatch;
        $this->limit = $limit;
    }

    public function add($data)
    {
        $this->dataSet[] = $data;
        if (count($this->dataSet) >= $this->limit) {
            call_user_func($this->doBatch, $this->dataSet, $this->limit);
            $this->dataSet = [];
        }
    }

    /**
     * 结束时要执行一些，处理剩下的数据
     */
    public function end()
    {
        if ($this->dataSet) {
            call_user_func($this->doBatch, $this->dataSet, $this->limit);
            $this->dataSet = [];
        }
    }


    /**
     * @param \Iterator $iterator
     * @param callable|null $correct
     * @param bool $continue_val
     * @return int
     */
    public function each(\Iterator $iterator, callable $correct = null, $continue_val = false){

        if ($correct) {
            $iterator = new IteratorConvert($iterator, $correct);
        }

        $num = 0;
        foreach ($iterator as $item) {
            if ($item === $continue_val) {
                continue;
            }
            $this->add($item);
            $num++;
        }

        $this->end();
        return $num;
    }
}