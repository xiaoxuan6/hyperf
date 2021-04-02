<?php

declare(strict_types=1);

namespace App\Nsq\Consumer;

use Hyperf\Nsq\AbstractConsumer;
use Hyperf\Nsq\Annotation\Consumer;
use Hyperf\Nsq\Message;
use Hyperf\Nsq\Result;

/**
 * @Consumer(topic="hyperf", channel="hyperf", name ="DemoConsumer", nums=1)
 */
class DemoConsumer extends AbstractConsumer
{
    public function consume(Message $payload): ?string
    {
        var_dump($payload->getBody());

        return Result::ACK;
    }

    /*
     * Notes: 局部控制消费队列是否启动
     * Date: 2021/3/27 16:54
     * @return bool
     */
//    public function isEnable(): bool
//    {
//        return true;
//    }
}
