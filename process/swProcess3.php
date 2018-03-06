<?php
    /**
     * 进程间通信 (内置管道通信 实为 unix socket 通信)
    swoole_process::__construct(callable $function, $redirect_stdin_stdout = false, $create_pipe = 1);
    参数名	                参数解释
    function	            进程需要执行的函数
    redirect_stdin_stdout	是否需要重定向标准输入输出，默认为否
    create_pipe	            创建管道的类型，1为流式，2为数据包模式，0代表不创建

    $function                是进程启动后执行的逻辑内容。这里需要注意，如果这个函数执行完，那么整个进程会退出并被自动销毁。所以如果你希望创建一个常驻进程并且一直执行任务，那么就需要在$function中添加一个死循环（或者Swoole的事件循环），由此来保证进程不会退出。
    $redirect_stdin_stdout   选项如果开启，Process会默认创建管道，并且将标准输入STDIN重定向为读取管道，将标准输出STDOUT重定向为写入管道。这个选项是用于配合Process的exec方法使用，具体使用场景将在后面的小节中介绍
    $create_pipe             用于指定是否创建管道以及管道的类型。如果选择流式管道，那么在管道中的数据是像TCP协议一样，不会自动拆包，需要在业务层实现分包逻辑；如果选择数据包模式，管道中发送的数据都会是一个个完整的数据包，读取的时候也不会出现一次读取多个数据的情况
     */

    /*
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
    */





    /**
     * 进程间通信 (消息队列)
     * 其中，useQueue的
     第一个参数msgkey是消息队列的键值，在系统中，使用同一个key创建的队列指向同一个实例，因此我们可以使用同一个key来让所有的子进程共享同一个消息队列，也可以使用不同的key使得各个子进程使用独立的消息队列。
     第二个参数mode有两种模式，1和2。
     如果使用模式1，那么使用指定子进程对象推送消息，该消息就会在指定的子进程中收到。比如我们创建了10个Process对象，并编号为1-10，然后使用$process_3->push("")发送消息的话，这条消息就会在第三个子进程里收到，可以在进程回调中，通过$process->pop()来获取这条消息（即使使用同一个key创建消息队列， 也会如此）。这个模式适用于需要通知特定子进程的时候使用。
     如果使用模式2，那么所有共用同一个消息队列的子进程会使用抢占模式争抢消息队列中的消息。还是用上面的10个子进程的例子，这10个子进程都在调用pop方法等待消息。如果这时使用任意一个子进程对象push一条消息进去，只会有其中某一个子进程抢到这个消息，其他子进程不会收到。这个模式适用于多进程消费模型，我们可以创建一个多个Process用于处理逻辑，然后使用消息队列来发逻辑，这样发送出去的任务会被自动被闲置的进程获取并执行。
     */
    function runner(swoole_process $sub_process)
    {
        echo $sub_process->pop();
        $sub_process->push("Hello Main Process .\n");
        $sub_process->exit(0);
    }

    $process = new swoole_process("runner", false, 1);
    // 使用模式1
    $process->useQueue(ftok(__FILE__, 'p'), 1);
    $process->start();

    $process->push("Hello Sub Process .\n");
    // 防止消息被父进程自己消费
    sleep(1);
    echo $process->pop();
    swoole_process::wait(true);

?>
