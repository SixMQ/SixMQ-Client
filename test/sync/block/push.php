<?php

require dirname(__DIR__, 2) . '/common.php';
use SixMQ\Client\Queue;
use SixMQ\Client\Network\Client;
use SixMQ\Client\Network\SendMessage;

go(function(){
    // 实例化客户端
    $client = Client::newInstance('127.0.0.1', 18086);

    // 连接
    if(!$client->connect())
    {
        echo 'connect gg!', PHP_EOL;
        return;
    }

    // 实例化队列
    $queue = new Queue($client, 'test12', 3);
    
    // 入队列
    var_dump($queue->push([
        'time'    =>    microtime(true),
    ], [
        'block' =>  -1,
    ]));

    // 关闭客户端连接
    $client->close();
});