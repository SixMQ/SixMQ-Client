<?php
namespace SixMQ\Client;

use SixMQ\Client\Network\IClient;
use SixMQ\Struct\Queue\Client\Pop;
use SixMQ\Struct\Queue\Client\Push;
use SixMQ\Struct\Queue\Client\Complete;
use SixMQ\Client\Network\SendMessage;

class Queue
{
	/**
	 * 客户端
	 *
	 * @var \SixMQ\Client\Network\IClient
	 */
	private $client;

	/**
	 * 队列ID
	 *
	 * @var string
	 */
	private $queueId;

	/**
	 * 任务超时时间，单位：秒
	 *
	 * @var double
	 */
	private $taskExpire;

	public function __construct(IClient $client, $queueId, $taskExpire)
	{
		$this->client = $client;
		$this->queueId = $queueId;
		$this->taskExpire = $taskExpire;
	}

	/**
	 * 消息入队列
	 *
	 * @param mixed $data
	 * @return boolean
	 */
	public function push($data)
	{
		$message = new Push($this->queueId, $data);
		$result = $this->client->sendMessage(new SendMessage($message));
		if(!$result)
		{
			return false;
		}
		return $result->getData()->success;
	}

	/**
	 * 消息出队列
	 *
	 * @return \SixMQ\Struct\Queue\Server\Pop
	 */
	public function pop()
	{
		$message = new Pop($this->queueId, $this->taskExpire);
		$result = $this->client->sendMessage(new SendMessage($message));
		if(!$result)
		{
			return null;
		}
		return $result->getData();
	}

	/**
	 * 消息处理完成
	 *
	 * @param string $messageId
	 * @param boolean $success
	 * @param mixed $data
	 * @return boolean
	 */
	public function complete($messageId, $success, $data = null)
	{
		$message = new Complete($this->queueId, $messageId, $success, $data);
		$result = $this->client->sendMessage(new SendMessage($message));
		if(!$result)
		{
			return false;
		}
		return $result->getData()->success;
	}
}