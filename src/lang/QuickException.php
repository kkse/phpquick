<?php
namespace kkse\quick\lang;

class QuickException extends \Exception
{
    protected $errcode = '';//错误码（字符串格式）
    protected $data = null;//附加数据

    /**
     * @return string
     */
    public function getErrcode()
    {
        return $this->errcode;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $msg
     * @param int|string $code
     * @param null $data
     * @throws QuickException
     */
    public static function throwError($msg, $code = 0, $data = null) {
        $errcode = '';
        if (!is_int($code)) {
            $errcode = strval($code);
            $code = 0;
        }
        $obj = new self($msg, $code);
        $obj->errcode = $errcode;
        $obj->data = $data;
        throw $obj;
    }

}