<?php
namespace SixMQ\Client\Network;

class SendMessage implements ISendMessage
{
	/**
	 * 数据
	 *
	 * @var mixed
	 */
	private $data;

	/**
	 * 超时时间，为空则默认，-1不限制
	 *
	 * @var float
	 */
	private $timeout;

	public function __construct($data = null, $timeout = null)
	{
		$this->data = $data;
		$this->timeout = $timeout;
	}

	/**
	 * 设置数据
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * 获取数据
	 *
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * 编码数据
	 *
	 * @return mixed
	 */
	public function encode()
	{
		$sendData = json_encode($this->data);
		return pack('Na*', strlen($sendData), $sendData);
	}
	
	/**
	 * 获取超时时间
	 *
	 * @return float
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}
}