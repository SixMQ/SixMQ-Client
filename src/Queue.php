<?php
namespace SixMQ\Client;

use SixMQ\Client\Network\IClient;
use SixMQ\Struct\Queue\Client\Pop;
use SixMQ\Struct\Queue\Client\Push;
use SixMQ\Client\Network\SendMessage;
use SixMQ\Struct\Queue\Client\Remove;
use SixMQ\Struct\Queue\Client\Complete;
use SixMQ\Struct\Queue\Client\GetMessage;

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
     * @param array $options
     * @return \SixMQ\Struct\Queue\Server\Push|null
     */
    public function push($data, $options = [])
    {
        $message = new Push($this->queueId, $data, $options);
        $result = $this->client->sendMessage(new SendMessage($message, $this->getTimeout(isset($options['block']) ? $options['block'] : 0)));
        if(!$result)
        {
            return null;
        }
        return $result->getData();
    }

    /**
     * 推送延迟消息
     *
     * @param mixed $data
     * @param float $delay
     * @param array $options
     * @return \SixMQ\Struct\Queue\Server\Push|null
     */
    public function pushDelay($data, $delay, $options = [])
    {
        if(!isset($options['delay']))
        {
            $options['delay'] = $delay;
        }
        $message = new Push($this->queueId, $data, $options);
        $result = $this->client->sendMessage(new SendMessage($message, $this->getTimeout(isset($options['block']) ? $options['block'] : 0)));
        if(!$result)
        {
            return null;
        }
        return $result->getData();
    }

    /**
     * 消息出队列
     *
     * @param boolean $block 是否阻塞等待返回，0：默认，立即返回；小于0：阻塞等待，不限制时长；大于0：阻塞等待时长，单位：秒
     * @return \SixMQ\Struct\Queue\Server\Pop|null
     */
    public function pop($block = 0)
    {
        $message = new Pop($this->queueId, $this->taskExpire, $block);

        $result = $this->client->sendMessage(new SendMessage($message, $this->getTimeout($block)));
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
        $message = new Complete($messageId, $success, $data);
        $result = $this->client->sendMessage(new SendMessage($message));
        if(!$result)
        {
            return false;
        }
        return $result->getData()->success;
    }

    /**
     * 获取消息数据
     *
     * @param string $messageId
     * @return \SixMQ\Struct\Queue\Server\GetMessage
     */
    public function getMessage($messageId)
    {
        $message = new GetMessage($messageId);

        $result = $this->client->sendMessage(new SendMessage($message));
        if(!$result)
        {
            return null;
        }
        return $result->getData();
    }

    /**
     * 将消息移出队列
     *
     * @param string $messageId
     * @return \SixMQ\Struct\Queue\Server\Reply
     */
    public function remove($messageId)
    {
        $message = new Remove($messageId);

        $result = $this->client->sendMessage(new SendMessage($message));
        if(!$result)
        {
            return null;
        }
        return $result->getData();
    }

    /**
     * 根据block获取timeout
     *
     * @param float $block
     * @return float|null
     */
    private function getTimeout($block)
    {
        if($block > 0)
        {
            return $block;
        }
        else if($block < 0)
        {
            return -1;
        }
        else
        {
            return null;
        }
    }
}