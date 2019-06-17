<?php
require __DIR__ . '/common.php';

use SixMQ\Client\Queue;

/**
 * 获取消息详情
 */
example(function(){
    $client = getExampleClient();

    $queueId = 'test1'; // 队列ID
    $taskExpire = 300; // 任务执行超时秒数

    // 实例化队列
    $queue = new Queue($client, $queueId, $taskExpire);
    
    // 消息ID
    $messageId = '';
    
    $result = $queue->getMessage($messageId);

    if($result->success)
    {
        echo 'GetMessage Success', PHP_EOL;
        var_dump($result->message);
    }
    else
    {
        echo 'GetMessage Failed: ', $result->error, PHP_EOL;
    }

});