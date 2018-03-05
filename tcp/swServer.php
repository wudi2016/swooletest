<?php
//echo 'sss';
$serv = new swoole_server("0.0.0.0", 9501);
$serv->set(array(
    'worker_num' => 1,
    'task_worker_num' => 3,
    'task_ipc_mode' => 1,
    'heartbeat_idle_time' => 5,
    'heartbeat_check_interval' => 2,
));

$serv->on('workerstart', function($serv, $id) {
    $redis = new redis;
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('wudiredis');
    $serv->redis = $redis;
});

$serv->on('Receive', function($serv, $fd, $from_id, $data) {
    $m = [
        'fd' =>  $fd,
        'data' => $data
    ];
//    if($data == 'ping'){
//        echo $data."\n";
//    }else{
//        $task_id = $serv->task($m);
//    }
    $task_id = $serv->task($m);
});

$serv->on('Task', function ($serv, $task_id, $from_id, $data) {
    //echo $data['data']."\n";
    //var_dump( $serv->redis->ping());
//    date_default_timezone_set('PRC');
//    $file = '/var/www/test/test.txt';
//    $cont = date('Y-m-d H:i:s').'超时测试成功！'.PHP_EOL;
//    file_put_contents($file,$cont,FILE_APPEND);
    $serv->finish($data);
});

$serv->on('Finish', function ($serv, $task_id, $data) {
    //echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
    $serv -> send($data['fd'],$data['data'].' success !'."\n");
});
$serv->start();
?>
