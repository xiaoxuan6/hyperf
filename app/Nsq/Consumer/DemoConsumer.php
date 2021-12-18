<?php

declare(strict_types=1);

namespace App\Nsq\Consumer;

use Hyperf\Nsq\AbstractConsumer;
use Hyperf\Nsq\Annotation\Consumer;
use Hyperf\Nsq\Message;
use Hyperf\Nsq\Result;

// 配置 	类型 	注解或抽象类默认值 	备注
// topic 	string 	‘’ 	要监听的 topic
// channel 	string 	‘’ 	要监听的 channel
// name 	string 	NsqConsumer 	消费者的名称
// nums 	int 	1 	消费者的进程数
// pool 	string 	default 	消费者对应的连接，对应配置文件的 key

/**
 * @Consumer(topic="hyperf", channel="hyperf", name ="DemoConsumer", nums=1)
 */
class DemoConsumer extends AbstractConsumer
{
    public function consume(Message $payload): ?string
    {
        echo $payload->getBody();

        return Result::ACK;
    }

    /*
     * Notes: 局部控制消费队列是否启动
     * Date: 2021/3/27 16:54
     * @return bool
     */
    public function isEnable(): bool
    {
        return true;
    }
}
