<?php
namespace SixMQ\Client\Network\Swoole;

use SixMQ\Client\Network\IClient;
use SixMQ\Client\Network\RecvMessage;
use Swoole\Coroutine;

class Client implements IClient
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

	/**
	 * 挂起的协程们
	 *
	 * @var array
	 */
	private $suspendCos = [];

	/**
	 * 协程接收的数据们
	 *
	 * @var array
	 */
	private $coReceives = [];

	/**
	 * 接收数据的标志
	 *
	 * @var int
	 */
	private $recevingFlag;

	/**
	 * 超时计时器
	 *
	 * @var int
	 */
	private $timeoutTimer;

	public function __construct($host, $port, $timeout = 3, $options = [])
	{
		$this->host = $host;
		$this->port = $port;
		$this->timeout = $timeout;
		$this->options = $options;
		$this->recevingFlag = static::RECEVING_FLAG_STOP;
		$this->client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
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
		if($result)
		{
			$this->timeoutTimer = swoole_timer_tick(1000, function(){
				foreach($this->suspendCos as $coid => $option)
				{
					if(microtime(true) - $option['time'] >= $this->timeout)
					{
						Coroutine::resume($coid);
					}
					else
					{
						break;
					}
				}
			});
			$this->startReceive();
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
		swoole_timer_clear($this->timeoutTimer);
		$this->stopReceive();
		while(static::RECEVING_FLAG_WAIT_STOP === $this->recevingFlag)
		{
			Coroutine::sleep(0.001);
		}
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
		$coid = Coroutine::getuid();
		$message->getData()->flag = $coid;
		if(!$this->client->send($message->encode()))
		{
			return false; // 发送失败
		}
		if(!isset($this->coReceives[$coid]))
		{
			$this->suspendCos[$coid] = [
				'time'	=>	microtime(true),
			];
			Coroutine::suspend();
		}
		unset($this->suspendCos[$coid]);
		if(isset($this->coReceives[$coid]))
		{
			$result = $this->coReceives[$coid];
			unset($this->coReceives[$coid]);
			return $result;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 开始接收
	 *
	 * @return void
	 */
	private function startReceive()
	{
		switch($this->recevingFlag)
		{
			case static::RECEVING_FLAG_WAIT_STOP:
				while(static::RECEVING_FLAG_WAIT_STOP === $this->recevingFlag)
				{
					Coroutine::sleep(0.001);
				}
				break;
			case static::RECEVING_FLAG_STOP:
				break;
			default:
				return false;
		}
		if(static::RECEVING_FLAG_STOP === $this->recevingFlag)
		{
			$this->recevingFlag = static::RECEVING_FLAG_START;
			go(function(){
				$this->receive();
			});
			return true;
		}
		return false;
	}

	/**
	 * 停止接收
	 *
	 * @return void
	 */
	private function stopReceive()
	{
		if(static::RECEVING_FLAG_START === $this->recevingFlag)
		{
			$this->recevingFlag = static::RECEVING_FLAG_WAIT_STOP;
		}
	}

	/**
	 * 接收
	 *
	 * @return void
	 */
	private function receive()
	{
		// Coroutine::sleep(0.1); // 防止$this->recevingCoID还未被赋值
		// $coid = Coroutine::getuid();
		// while($coid === $this->recevingCoID && $this->client->isConnected())
		while(static::RECEVING_FLAG_START === $this->recevingFlag && $this->client->isConnected())
		{
			$data = $this->client->recv();
			if('' === $data || false === $data)
			{
				continue;
			}
			$message = new RecvMessage($data);
			$flag = $message->getData()->flag;
			if(is_int($flag) && isset($this->suspendCos[$flag]))
			{
				$this->coReceives[$flag] = $message;
				Coroutine::resume($flag);
			}
			else
			{

			}
		}
		$this->recevingFlag = static::RECEVING_FLAG_STOP;
	}
}