<?php
namespace SixMQ\Client\Network;

interface ISendMessage
{
	/**
	 * 设置数据
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function setData($data);

	/**
	 * 获取数据
	 *
	 * @return mixed
	 */
	public function getData();

	/**
	 * 编码数据
	 *
	 * @return mixed
	 */
	public function encode();
}