<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/7/18
 * Time: 16:32
 */

namespace kkse\quick\tool;

/**
 * 迭代器转换，只提供值的转换，不提供key转换
 * Class IteratorConvert
 * @package Com\Tool
 */
class IteratorConvert implements \Iterator
{
    protected $iterator;
    protected $callConvert;
    /**
     * IteratorConvert constructor.
     * @param \Iterator $iterator 原迭代器
     * @param callable $callConvert 转换处理
     */
    public function __construct(\Iterator $iterator, callable $callConvert)
    {
        $this->iterator = $iterator;
        $this->callConvert = $callConvert;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return call_user_func($this->callConvert, $this->iterator->current());
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }
}