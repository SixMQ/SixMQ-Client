<?php
namespace SixMQ\Client\Network\Sync;

use SixMQ\Client\Network\IClient;
use SixMQ\Client\Network\RecvMessage;

class Client implements IClient
{
    /**
     * 服务器地址
     *
     * @var string
     */
    public $host;

    /**
     * 服务器端口
     *
     * @var int
     */
    public $port;

    /**
     * 网络超时时间，单位：秒
     *
     * @var integer
     */
    public $timeout;

    /**
     * 其它选项
     *
     * @var array
     */
    public $options;

    /**
     * 连接句柄
     *
     * @var \resource
     */
    private $client;

    public function __construct($host, $port, $timeout = 3, $options = [])
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->options = $options;
    }

    public function __destruct()
    {
        if(null !== $this->isConnected())
        {
            $this->close();
        }
    }

    /**
     * 连接服务器
     *
     * @return boolean
     */
    public function connect()
    {
        $client = stream_socket_client("tcp://{$this->host}:{$this->port}", $errno, $errstr, $this->timeout);
        $result = false !== $client;
        if($result)
        {
            $this->client = $client;
            stream_set_timeout($client, 0, $this->timeout * 1000000);
        }
        else
        {
            throw new \Exception(sprintf('SixMQ connect fail, code: %s, message: %s', $errno, $errstr));
        }
        return $result;
    }

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close()
    {
        if(null !== $this->client)
        {
            fclose($this->client);
            $this->client = null;
        }
    }

    /**
     * 是否已连接
     *
     * @return boolean
     */
    public function isConnected()
    {
        return null !== $this->client;
    }

    /**
     * 发送消息
     *
     * @param \SixMQ\Client\Network\ISendMessage $message
     * @return IRecvMessage|boolean
     */
    public function sendMessage(\SixMQ\Client\Network\ISendMessage $message)
    {
        if(false === fwrite($this->client, $message->encode()))
        {
            return false; // 发送失败
        }
        $timeout = $message->getTimeout();
        if(null === $timeout)
        {
            $timeout = $this->timeout;
        }
        return $this->receive($timeout);
    }

    /**
     * 接收
     *
     * @return void
     */
    private function receive($timeout = null)
    {
        if(null === $timeout)
        {
            stream_set_timeout($this->client, 0, $this->timeout * 1000000);
        }
        else if(-1 === $timeout)
        {
            stream_set_timeout($this->client, PHP_INT_MAX);
        }
        else {
            stream_set_timeout($this->client, 0, $timeout * 1000000);
        }
        $versionData = fread($this->client, 4);
        if(!$versionData)
        {
            return false;
        }
        $lengthData = fread($this->client, 4);
        if(!$lengthData)
        {
            return false;
        }
        $length = unpack('N', $lengthData)[1];
        $left = $length;
        $data = '';
        while ($left > 0 && !feof($this->client))
        {
            $chunk = fread($this->client, min(8192, $left));
            if ($chunk)
            {
                $data .= $chunk;
                $left -= strlen($chunk);
            }
        }
        $message = new RecvMessage($versionData . $lengthData . $data);
        return $message;
    }

}