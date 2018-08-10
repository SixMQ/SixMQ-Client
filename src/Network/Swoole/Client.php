<?php
namespace SixMQ\Client\Network\Swoole;

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
	 * 其它选项
	 *
	 * @var array
	 */
	public $options;

	/**
	 * Swoole 协程客户端
	 *
	 * @var \Swoole\Coroutine\Client
	 */
	private $client;

	public function __construct($host, $port, $options = [])
	{
		$this->host = $host;
		$this->port = $port;
		$this->options = $options;
		$this->client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
	}

	/**
	 * 连接服务器
	 *
	 * @return boolean
	 */
	public function connect()
	{
		return $this->client->connect($this->host, $this->port, $this->options['timeout'] ?? 3);
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
			return false; // 发送失败
		}
		// var_dump('recv');
		$data = $this->client->recv();
		if(!$data)
		{
			return false; // 接收失败
		}
		return new RecvMessage($data);
	}

}