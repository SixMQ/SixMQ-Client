<?php
namespace SixMQ\Client\Network;

class SendMessage implements ISendMessage
{
	private $data;

	public function __construct($data)
	{
		$this->data = $data;
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