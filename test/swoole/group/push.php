<?php

use SixMQ\Client\Network\SendMessage;
use SixMQ\Client\Queue;
require dirname(__DIR__, 2) . '/common.php';

go(function(){
    // 实例化客户端
    $client = new \SixMQ\Client\Network\Swoole\Client('127.0.0.1', 18086);

    // 连接
    if(!$client->connect())
    {
        echo 'connect gg!', PHP_EOL;
        return;
    }

    // 实例化队列
    $queue = new Queue($client, 'test1', 3);
    
    // 入队列
    var_dump($queue->push([
        'time'    =>    microtime(true),
    ], [
        'groupId'   =>  'testGroup1',
        // 'block'     =>  -1,
    ]));

    // 关闭客户端连接
    $client->close();
});