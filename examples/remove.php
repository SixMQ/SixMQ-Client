<?php
require __DIR__ . '/common.php';

use SixMQ\Client\Queue;

/**
 * 移除消息
 */
example(function(){
    $client = getExampleClient();

    $queueId = 'test1'; // 队列ID
    $taskExpire = 300; // 任务执行超时秒数

    // 实例化队列
    $queue = new Queue($client, $queueId, $taskExpire);
    
    // 消息ID
    $messageId = '';
    
    $result = $queue->remove($messageId);

    if($result->success)
    {
        echo 'Remove message Success', PHP_EOL;
    }
    else
    {
        echo 'Remove message Failed: ', $result->error, PHP_EOL;
    }

});