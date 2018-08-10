<?php
namespace SixMQ\Client\Network;

interface ISendMessage
{
	/**
	 * 编码数据
	 *
	 * @return mixed
	 */
	public function encode();
}