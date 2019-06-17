<?php
require __DIR__ . '/common.php';

use SixMQ\Client\Queue;

/**
 * pop 阻塞等待返回
 */
example(function(){
    $client = getExampleClient();

    $queueId = 'test1'; // 队列ID
    $taskExpire = null; // 消费端此值传什么都无效

    // 实例化队列
    $queue = new Queue($client, $queueId, $taskExpire);
    
    $result = $queue->pop(86400); // 这里设置为最长等待一天

    if($result->success)
    {
        echo 'Pop Success', PHP_EOL, 'data:', PHP_EOL;
        var_dump($result->data);
        $success = true;
        // 消费成功或失败的反馈
        $queue->complete($result->messageId, $success, 'success');
    }
    else
    {
        echo 'Pop Failed: ', $result->error, PHP_EOL;
        return;
    }

});