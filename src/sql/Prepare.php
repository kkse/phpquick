<?php
namespace kkse\quick\sql;

/**
 * sql执行的预定义类
 * Class Prepare
 * @package kkse\quick\sql
 */
class Prepare
{
    protected $mode;
    protected $sql;
    protected $parameters;
    public function __construct($mode, $sql, array $parameters)
    {
        $this->mode = $mode;
        $this->sql = $sql;
        $this->parameters = $parameters;
    }

    public function getMode(){
        return $this->mode;
    }
    public function getSql(){
        return $this->sql;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}