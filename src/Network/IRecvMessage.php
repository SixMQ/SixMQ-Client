<?php
namespace SixMQ\Client\Network;

interface IRecvMessage
{
	public function __construct($data);

	/**
	 * 获取服务端版本
	 *
	 * @return string
	 */
	public function getVersion();

	/**
	 * 获取消息长度
	 *
	 * @return int
	 */
	public function getLength();

	/**
	 * 获取数据
	 *
	 * @return mixed
	 */
	public function getData();
}