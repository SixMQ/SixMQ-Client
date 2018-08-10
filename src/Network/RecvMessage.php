<?php
namespace SixMQ\Client\Network;

class RecvMessage implements IRecvMessage
{
	/**
	 * 客户端版本
	 *
	 * @var int
	 */
	private $version;

	/**
	 * 数据长度
	 *
	 * @var int
	 */
	private $length;

	/**
	 * 数据
	 *
	 * @var mixed
	 */
	private $data;

	public function __construct($data)
	{
		$list = unpack('Nversion/Nlength/a*data', $data);
		$this->version = $list['version'];
		$this->length = $list['length'];
		$this->data = json_decode($list['data']);
	}

	/**
	 * 获取服务端版本
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * 获取消息长度
	 *
	 * @return int
	 */
	public function getLength()
	{
		return $this->length;
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
}