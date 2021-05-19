<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Amqp\Message\Type;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * 添加该方法会自动执行消费队列（前提是 enable = true）
 * @Consumer()
 */
// exchange="hyperf", routingKey="hyperf", queue="hyperf", name ="DemoConsumer", nums=1
class DemoConsumer extends ConsumerMessage
{
    /**
     * 交换机名称：声明自己对 生产时 创建的哪个Exchange感兴趣
     * @var string
     */
    protected $exchange = "im.message.fanout";

    /**
     * 交换机类型
     * @var string
     */
    protected $type = Type::FANOUT;

    /**
     * 队列名称：将自己的Queue绑定到自己感兴趣的路由关键字上
     * @var string
     */
    protected $queue = "amqp";

    /**
     * 路由key：生产时指定的路由key
     * @var string
     */
    protected $routingKey = "consumer:im:message";

    /**
     * 是否根据服务自启 true是 false否
     * @var bool
     */
//    protected $enable = true;

    /**
     * Notes: 消费队列消息
     * Date: 2021/5/18 9:49
     * @param $data
     * @param AMQPMessage $message
     * @return string
     */
    public function consumeMessage($data, AMQPMessage $message): string
    {
        var_dump("消费队列消息：" . $data);

        return Result::ACK;
    }

    /**
     * 是否根据服务自启 true是 false否
     *
     *  如果设置为 false，不跟随服务器启动，可通过 Command 手动处理消息
     * @var bool
     */
    public function isEnable(): bool
    {
        return false;
    }
}
