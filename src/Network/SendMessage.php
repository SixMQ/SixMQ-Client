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

	public function __construct($data = null)
	{
		$this->data = $data;
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
}