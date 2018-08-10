<?php

use SixMQ\Client\Network\SendMessage;
require __DIR__ . '/common.php';

go(function(){
	$client = new \SixMQ\Client\Network\Swoole\Client('127.0.0.1', 18086);
	var_dump($client->connect());
	$time = microtime(true);
	// for($i = 0; $i < 1; ++$i)
	while(microtime(true) - $time < 60)
	{
		$client->sendMessage(new SendMessage([
			'action'	=>	'queue.push',
			'queueId'	=>	'test1',
			'data'		=>	[
				'time'	=>	microtime(true),
			],
		]));
		// usleep(1000);
	}
});