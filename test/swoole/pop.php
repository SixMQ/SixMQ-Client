<?php

require __DIR__ . '/common.php';
use SixMQ\Client\Network\SendMessage;
use Swoole\Coroutine;

for($i = 0; $i < 2; ++$i)
go(function(){
	$client = new \SixMQ\Client\Network\Swoole\Client('127.0.0.1', 18086);
	var_dump($client->connect());
	while(true)
	{
		$message = $client->sendMessage(new SendMessage([
			'action'	=>	'queue.pop',
			'queueId'	=>	'test1',
		]));
		$data = $message->getData();
		// var_dump('success:', $data->success);
		if($data->success)
		{
			// echo date('Y-m-d H:i:s', $data->data->data->time), PHP_EOL;
			$s = date('s', $data->data->data->time);
			// echo '$s=', $s, PHP_EOL;
			$client->sendMessage(new SendMessage([
				'action'	=>	'queue.complete',
				'success'	=>	true,
				'queueId'	=>	$data->queueId,
				'messageId'	=>	$data->messageId,
				'data'		=>	null,
			]));
		}
		else
		{
			Coroutine::sleep(0.1);
		}
		// break;
	}
});