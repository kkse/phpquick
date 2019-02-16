<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/12/25
 * Time: 16:55
 */

namespace kkse\quick\lang;


abstract class SessionHandler implements \SessionHandlerInterface
{
    /**
     * 表示启动中的Session处理器,
     * 如果session_status()值为PHP_SESSION_ACTIVE，但该值是null的话，就表明session是启用了框架外的session
     * @var null|self
     */
    private static $running = null;

    /**
     * @param $session_id
     * @return bool
     */
    abstract protected function delete($session_id);

    abstract public function gc($maxlifetime);

    protected function isRunning()
    {
        if (self::$running && self::$running === $this) {
            return true;
        }
        return false;
    }

    public function close()
    {
        if ($this->isRunning()) {
            self::$running = null;
        }
    }

    public function destroy($session_id)
    {
        return $this->delete($session_id);
    }


    public function open($save_path, $session_name)
    {
        return true;
    }

    public function read($session_id)
    {

    }

    public function write($session_id, $session_data)
    {

    }

    public function setIndex($session_id, $index)
    {

    }
}