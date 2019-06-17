# SixMQ PHP Client

SixMQ 操作顺序：`客户端->队列->消息`

## 客户端

客户端用于和服务端通信，SixMQ 的客户端支持同步和协程两种模式，其中同步可在所有场景下使用，而协程则必须运行在 Swoole 下。

同步 Client 类：`\SixMQ\Client\Network\Sync\Client`

协程 Client 类：`\SixMQ\Client\Network\Swoole\Client`

### 客户端实例化

```php
$client = \SixMQ\Client\Network\Client::newInstance($host, $port);
```

## 队列

要操作队列，需要先实例化队列对象。

### 实例化队列

```php
$queue = new Queue(
    $client,        // 客户端对象
    $queueId,       // 队列ID
    $taskExpire     // 任务执行超时时间
);
```

### 消息入队列

```php
$result = $queue->push(
    $data,          // 数据，可以是任何格式
    $options = []   // push选项，请看下文
);
```

**push选项**

```php
// 所有选项皆为可选
$options = [
    /**
     * 消息分组Id
  */
    'groupId'   =>  null,

    /**
     * 是否阻塞等待返回
     * 0：默认，立即返回
     * 小于0：阻塞等待，不限制时长
     * 大于0：阻塞等待时长，单位：秒
  */
    'block'     =>  0,

    /**
     * 消费失败重试次数
  */
    'retry'     =>  3,

    /**
     * 超时时间，单位：秒，-1则为不限制
     * 超过超时时间则从队列中移除
  */
    'timeout'   =>  -1,

    /**
     * 延迟执行的秒数，支持小数
  */
    'delay'     =>  null,

];
```

**返回值格式**

```php
/**
 * 操作是否执行成功
 *
 * @var boolean
 */
public $success;

/**
 * 错误信息
 *
 * @var string
 */
public $error;

/**
 * 标志符，请求时传入的原样返回
 *
 * @var string
 */
public $flag;

/**
 * 队列ID
 *
 * @var string
 */
public $queueId;

/**
 * 消息ID
 *
 * @var string
 */
public $messageId;

/**
 * 消息是否被消费过
 *
 * @var boolean
 */
public $consum = false;

/**
 * 消息是否成功消费
 * 只有当$consum为true时，才有效
 *
 * @var boolean
 */
public $resultSuccess = false;

/**
 * 消费结果数据
 *
 * @var mixed
 */
public $resultData;
```

### 推送延迟消息

相当于 `push()` 方法往 `$options` 中加了 `delay` 选项。

```php
$queue->pushDelay(
    $data,          // 数据，可以是任何格式
    $delay,         // 延迟执行的秒数，支持小数
    $options = []   // push选项，请看push中的说明
);
```

**返回值格式**

同上

### 消息出队列

```php
$queue->pop(
    $block = 0 // 是否阻塞等待返回，0：默认，立即返回；小于0：阻塞等待，不限制时长；大于0：阻塞等待时长，单位：秒
);
```

**返回值格式**

```php
/**
 * 操作是否执行成功
 *
 * @var boolean
 */
public $success;

/**
 * 错误信息
 *
 * @var string
 */
public $error;

/**
 * 标志符，请求时传入的原样返回
 *
 * @var string
 */
public $flag;

/**
 * 队列ID
 *
 * @var string
 */
public $queueId;

/**
 * 消息ID
 *
 * @var string
 */
public $messageId;

/**
 * 数据
 *
 * @var mixed
 */
public $data;
```
### 消息处理完成

```php
$queue->complete(
    $messageId,     // 消息ID
    $success,       // 是否成功
    $data = null    // 消费数据，可选
)
```

**返回值格式**

`boolean`

### 获取消息数据

```php
$queue->getMessage(
    $messageId
);
```

**返回值格式**

```php
/**
 * 操作是否执行成功
 *
 * @var boolean
 */
public $success;

/**
 * 错误信息
 *
 * @var string
 */
public $error;

/**
 * 标志符，请求时传入的原样返回
 *
 * @var string
 */
public $flag;

/**
 * 消息ID
 *
 * @var string
 */
public $messageId;

/**
 * 消息数据
 *
 * @var mixed
 */
public $message;
```

### 将消息移出队列

```php
$queue->remove($messageId);
```

**返回值格式**

```php
/**
 * 操作是否执行成功
 *
 * @var boolean
 */
public $success;

/**
 * 错误信息
 *
 * @var string
 */
public $error;

/**
 * 标志符，请求时传入的原样返回
 *
 * @var string
 */
public $flag;
```
