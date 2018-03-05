<?php
    $info = array(
        "sendmail"=>1,
        "mailto"=>"12345@qq.com",
        "sendsms"=>1,
        "smsto"=>"123456"
    );
    echo "start:".date("Y-m-d H:i:s").PHP_EOL;
    $mail_process = new swoole_process('sendMail',true);
    $mail_process->start();
    $sms_process = new swoole_process('sendSMS',true);
    $sms_process->start();
    //主进程输出子进程范围内容
    echo $mail_process->read();
    echo PHP_EOL;
    echo $sms_process->read();
    echo PHP_EOL;
    echo "end:".date("Y-m-d H:i:s").PHP_EOL;
    //并行函数
    function sendMail(swoole_process $worker){
        global $info;
        if($info['sendmail']==1){
            sleep(3);
            $worker->write("send mail to ".$info['mailto']);
        }
    }
    function sendSMS(swoole_process $worker){
        global $info;
        if($info['sendmail']==1){
            sleep(4);
            $worker->write("send sms to ".$info['smsto']);
        }
    }
?>
