<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/2/28
 * Time: 14:52
 */

namespace kkse\quick\lang;


class Storage
{
    public static $mkdir_mode = 0755;
    protected $true_file_path;//文件全路径
    protected $file_path;//文件全路径
    protected $dir;//所属目录
    protected $file_basename;//文件名
    protected $file_filename;//文件名称
    protected $file_extension = '';//文件后缀扩展名

    protected $file_prefix = '';//文件前缀
    protected $file_suffix = '';//文件后缀

    protected $is_file = false;//是不是真实的文件

    public function __construct($file_path)
    {
        $this->file_path = $file_path;
        $path_parts = pathinfo($file_path);
        isset($path_parts['dirname']) and $this->dir = $path_parts['dirname'];
        isset($path_parts['basename']) and $this->file_basename = $path_parts['basename'];

        isset($path_parts['filename']) and $this->file_filename = $path_parts['filename'];
        isset($path_parts['extension']) and $this->file_extension = $path_parts['extension'];

        $this->updateTrueFilePath();
    }

    protected function updateTrueFilePath()
    {
        $this->true_file_path = $this->dir.'/'.$this->file_prefix.$this->file_filename.$this->file_suffix;
        $this->file_extension === '' or $this->true_file_path .= '.'.$this->file_extension;

        $this->is_file = is_file($this->true_file_path);
    }

    public function setPrefix($file_prefix)
    {
        $this->file_prefix = $file_prefix;
        $this->updateTrueFilePath();
        return $this;
    }

    public function setSuffix($file_suffix)
    {
        $this->file_suffix = $file_suffix;
        $this->updateTrueFilePath();
        return $this;
    }

    public function include($once = false)
    {
        if ($this->is_file) {
            if ($once) {
                include_once($this->true_file_path);
            } else {
                include($this->true_file_path);
            }
        }
    }

    public function isFile()
    {
        return $this->is_file;
    }

    public function putContents($data, $flags = 0)
    {
        is_dir($this->dir) or mkdir($this->dir, self::$mkdir_mode, true);
        file_put_contents($this->true_file_path, $data, $flags)
        and $this->is_file = true;
    }

    public function __toString()
    {
        return $this->true_file_path;
    }
}