<?php

require dirname(__DIR__, 2) . '/common.php';
use Swoole\Coroutine;
use SixMQ\Client\Queue;
use SixMQ\Client\Network\SendMessage;

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

    // 从队列中弹出
    $data = $queue->pop();

    // 判断是否有数据
    if($data && $data->success)
    {
        // 队列中有数据
        $s = date('Y-m-d H:i:s', $data->data->data->time);
        echo '$s=', $s, PHP_EOL;
        $queue->complete($data->messageId, true, $s);
    }
    else
    {
        // 队列中无数据
        echo 'queue no data', PHP_EOL;
    }
    
    // 关闭客户端连接
    $client->close();
});