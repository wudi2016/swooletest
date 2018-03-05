<?php
    date_default_timezone_set('PRC');
    //$client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
    $client = new swoole_client(SWOOLE_SOCK_TCP);

    /*
    //注册连接成功回调
    $client->on("connect", function($cli) {
        //$cli->send("async hello\n");
        swoole_timer_tick(2000, function ()use ($cli) {
            $cli->send("ping");
        });
        //echo 'connetctd success';
    });

    //注册数据接收回调
    $client->on("receive", function($cli, $data){
        echo "Received: ".$data."\n";
    });

    //注册连接失败回调
    $client->on("error", function($cli){
        echo "Connect failed\n";
    });

    //注册连接关闭回调
    $client->on("close", function($cli){
        echo "Connection close\n";
    });
    */
    if(!$client->connect('127.0.0.1', 9501)){
        exit("async_task连接失败\n");
    }

    //var_dump($client->isConnected());
//    swoole_timer_tick(6000, function ()use ($client) {
//        $startTime = strtotime(date("Y-m-d 15:13:00"));
//        $endTime = strtotime(date("Y-m-d 15:35:00"));
//        $nowTime = time();
//        if($nowTime >= $startTime && $nowTime <= $endTime) {
//            $client->send("hello world");
//            echo $client->recv();
//        }else{
//            $client->send("ping");
//        }
//
//    });

    $client->send('1234');
    echo $client->recv();

    //echo "sync hello\n";
?>
