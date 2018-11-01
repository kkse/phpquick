<?php
namespace kkse\quick\cli;

use const kkse\quick\CURRENT_OS_IS_LINUX as IS_LINUX;

/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/8/13
 * Time: 14:50
 */
class BaseProc
{

    public function fork(){
        if (IS_LINUX) {
            $this->forkLinux();
        } else {
            $this->forkWindows();
        }
    }

    protected function forkLinux(){
        $pid = pcntl_fork();
        //父进程和子进程都会执行下面代码
        if ($pid == -1) {
            //错误处理：创建子进程失败时返回-1.
            die('could not fork');
        } elseif ($pid) {
            //父进程会得到子进程号，所以这里是父进程执行的逻辑
            pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程。
        } else {
            //子进程得到的$pid为0, 所以这里是子进程执行的逻辑。
        }
    }

    protected function forkWindows(){

    }
}

