<?php
$server = new swoole_websocket_server("0.0.0.0", 9501);

$server->set(array(
    //'heartbeat_idle_time' => 5,
    //'heartbeat_check_interval' => 2,
));

$server->on('open', function (swoole_websocket_server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->on('workerStart',function (swoole_server $serv, $worker_id){
    $serv->tick(2000, function()use ($serv){
        $start_fd = 0;
        while(true)
        {
            $conn_list = $serv->connection_list($start_fd, 10);
            if($conn_list===false or count($conn_list) === 0)
            {
                echo "finish\n";
                break;
            }
            $start_fd = end($conn_list);
            //var_dump($conn_list);
            foreach($conn_list as $fd)
            {
                $serv->push($fd, "advertisement");
            }
        }
    });
});

$server->start();

?>
