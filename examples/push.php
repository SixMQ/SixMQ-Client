<?php
require __DIR__ . '/common.php';

use SixMQ\Client\Queue;

/**
 * 最简单的入队列（push）示例
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
    
    $result = $queue->push($data);

    if($result->success)
    {
        echo 'Push Success', PHP_EOL;
    }
    else
    {
        echo 'Push Failed: ', $result->error, PHP_EOL;
    }

});