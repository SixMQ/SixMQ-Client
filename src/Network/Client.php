<?php
namespace SixMQ\Client\Network;

abstract class Client
{
    /**
     * 客户端类
     *
     * @var string
     */
    private static $handlerClass = \SixMQ\Client\Network\Sync\Client::class;

    /**
     * 实例化客户端
     *
     * @param string $host
     * @param int $port
     * @param array $options
     * @param string $handlerClass
     * @return \SixMQ\Client\Network\IClient
     */
    public static function newInstance($host, $port, $timeout = 3, $options = [], $handlerClass = null)
    {
        if(null === $handlerClass)
        {
            $handlerClass = static::$handlerClass;
        }
        return new $handlerClass($host, $port, $timeout, $options);
    }

    /**
     * 设置客户端类
     *
     * @param string $handlerClass
     * @return void
     */
    public static function setHandler($handlerClass)
    {
        static::$handlerClass = $handlerClass;
    }

    /**
     * 获取客户端类
     *
     * @return string
     */
    public static function getHandler()
    {
        return static::$handlerClass;
    }
}