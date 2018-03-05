<?php
    function runner(swoole_process $sub_process)
    {
        // 读取主进程发来的消息
        echo $sub_process->read();
        // 发送消息给主进程
        $sub_process->write("Hello Main Process\n");
        $sub_process->exit(0);
    }

    $process = new swoole_process("runner", false, 1);
    $process->start();
    // 发送消息给子进程
    $process->write("Hello Sub Process\n");
    // 读取子进程发来的消息
    echo $process->read();
    // 调用wait方法回收子进程
    swoole_process::wait(true);
?>
