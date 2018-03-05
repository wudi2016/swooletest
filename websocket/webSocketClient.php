<?php
    //var_dump($client->isConnected());

    $cli = new swoole_http_client('127.0.0.1', 9501);

    $cli->on('message', function ($_cli, $frame) {
        //var_dump($frame->data.PHP_EOL);
        echo $frame->data.PHP_EOL;
    });

    $cli->upgrade('/', function ($cli) {
        //$cli->push("pong",0x9);
        //$cli->push("ping");

        swoole_timer_tick(2000, function ()use ($cli) {
            $startTime = strtotime(date("Y-m-d 15:13:00"));
            $endTime = strtotime(date("Y-m-d 15:35:00"));
            $nowTime = time();

            if($nowTime >= $startTime && $nowTime <= $endTime) {
                $cli->push("hello world");
            }else{
                //$cli->push("ping");
                //$cli->push("pong",0x9);
            }
            //echo 'hello'.PHP_EOL;

        });
    });

