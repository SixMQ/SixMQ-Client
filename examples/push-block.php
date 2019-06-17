<?php
require __DIR__ . '/common.php';

use SixMQ\Client\Queue;

/**
 * push 阻塞等待返回
 */
example(function(){
    $client = getExampleClient();

    $queueId = 'test1'; // 队列ID
    $taskExpire = 300; // 任务执行超时秒数

    // 实例化队列
    $queue = new Queue($client, $queueId, $taskExpire);
    
    // 入队列
    $data = [
        'time'    =>    microtime(true),
    ];
    
    $result = $queue->push($data, [
        'block'     =>  86400, // 这里设置为最长等待一天
    ]);

    if($result->success)
    {
        echo 'Push returned', PHP_EOL;
        var_dump($result->resultSuccess, $result->resultData);
    }
    else
    {
        echo 'Push Failed: ', $result->error, PHP_EOL;
    }

});