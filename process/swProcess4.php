<?php
    $process = [];

    for($i = 0; $i < 3; $i ++){
        $process[$i] = new swoole_process(function(swoole_process $sub_process) use ($i){
            while(true)
            {
                $data = $sub_process->pop();
                if( $data == 'exit' )
                {
                    $sub_process->exit(0);
                    break;
                }
                echo $i . ": " .$data;
                sleep(1);
            }
        }, false, 1);
        $process[$i]->useQueue(ftok(__FILE__, 'p'), 2);
        $process[$i]->start();
    }
    // 发送消息
    $process[0]->push("Hello Sub Process\n");
    $process[0]->push("Hello Sub Process\n");
    $process[0]->push("Hello Sub Process\n");
    $process[0]->push("Hello Sub Process\n");
    // 通知子进程关闭
    $process[0]->push("exit");
    $process[0]->push("exit");
    $process[0]->push("exit");
    sleep(1);
    swoole_process::wait(true);
    swoole_process::wait(true);
    swoole_process::wait(true);
?>
