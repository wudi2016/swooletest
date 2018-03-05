<?php

class test{

    static $worker = null;		//存储进程实例
    static $limit = 0;

    public function __construct()
    {
        $this->mainAction();
    }

    public function mainAction()
    {
        $count = 1;
        //开启进程
        for($i=0;$i<$count ; $i++){
            $process = new swoole_process(array($this, 'thread'));
            $pid = $process->start();
            echo 'parent or son id:'.$pid.PHP_EOL;
            self::$worker[$pid] = $process;
        }

        swoole_process::signal(SIGCHLD, function(){		//rec child process,and parent process has being alive
            $status = swoole_process::wait();
            //after rec, process should be create a new process
            echo "kill#{$status['pid']} \n";
            $process = self::$worker[$status['pid']];
            unset(self::$worker[$status['pid']]);
            $pid = $process->start();
            self::$worker[$pid] = $process;
            echo "Create#{$pid} \n";
        });
    }

    public function thread(swoole_process $worker){
        $time = intval(date('Hi'));
        if(($time >= 930 && $time <= 1130) || ($time >= 1300 && $time <= 1500)) {
            //while (true) {
            //    usleep(2000);
            //}

            //self::$limit++;
            sleep(60);
            echo 'son end'.PHP_EOL;
        }else{
            if(intval(date('i')) == 30 || intval(date('i')) == 0){
                $this->logger->info("正在运行。。。");
            }
        }
    }
}

new test();


