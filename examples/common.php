<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use SixMQ\Client\Network\Client;

/**
 * 运行示例
 *
 * @param callable $callable
 * @return void
 */
function example($callable)
{
    if(function_exists('go'))
    {
        go($callable);
    }
    else
    {
        $callable();
    }
}

/**
 * 获取示例客户端对象
 *
 * @return \SixMQ\Client\Network\IClient
 */
function getExampleClient()
{
    // 实例化客户端
    $client = Client::newInstance(SIXMQ_EXAMPLE_HOST, SIXMQ_EXAMPLE_PORT);

    // 连接
    if(!$client->connect())
    {
        throw new \RuntimeException('Connect Failed');
    }

    return $client;
}

/**
 * 客户端设置
 */
Client::setHandler(\SixMQ\Client\Network\Sync\Client::class);   // 同步客户端
// Client::setHandler(\SixMQ\Client\Network\Swoole\Client::class); // Swoole 客户端

/**
 * 连接配置
 */
define('SIXMQ_EXAMPLE_HOST', '127.0.0.1');
define('SIXMQ_EXAMPLE_PORT', 18086);
