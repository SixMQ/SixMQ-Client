<?php
namespace SixMQ\Client\Network\Swoole;

use SixMQ\Client\Network\BaseClient;
use SixMQ\Client\Network\RecvMessage;

class Client extends BaseClient
{
    /**
     * 开始接收
     */
    const RECEVING_FLAG_START = 1;

    /**
     * 等待停止
     */
    const RECEVING_FLAG_WAIT_STOP = 2;

    /**
     * 停止接收
     */
    const RECEVING_FLAG_STOP = 3;

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
     * Swoole 协程客户端
     *
     * @param string $host
     * @param int $port
     * @param array $options
     * @var \Swoole\Coroutine\Client
     */
    private $client;

    public function __construct($host, $port, $timeout = 3, $options = [])
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->options = $options;
        $this->client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
        $this->client->set([
            'open_eof_split'        => false,
            'open_length_check'     => true,
            'package_length_type'   => 'N',
            'package_length_offset' => 4,       //第N个字节是包长度的值
            'package_body_offset'   => 8,       //第几个字节开始计算长度
            'package_max_length'    => 2 * 1024 * 1024,  //协议最大长度，默认2M
        ]);
    }

    public function __destruct()
    {
        if($this->client->isConnected())
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
        $result = $this->client->connect($this->host, $this->port, $this->timeout);
        return $result;
    }

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close()
    {
        $this->client->close();
    }

    /**
     * 是否已连接
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->client->isConnected();
    }

    /**
     * 发送消息
     *
     * @param \SixMQ\Client\Network\ISendMessage $message
     * @return IRecvMessage|boolean
     */
    public function sendMessage(\SixMQ\Client\Network\ISendMessage $message)
    {
        if(!$this->client->send($message->encode()))
        {
            $this->client->close();
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
     * @return \SixMQ\Client\Network\RecvMessage|boolean
     */
    private function receive($timeout)
    {
        $data = $this->client->recv($timeout);
        if('' === $data)
        {
            $this->client->close();
            return false;
        }
        if(false === $data)
        {
            if(110 !== $this->client->errCode)
            {
                $this->client->close();
            }
            return false;
        }
        $message = new RecvMessage($data);
        return $message;
    }
}