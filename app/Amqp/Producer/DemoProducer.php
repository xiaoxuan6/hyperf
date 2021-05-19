<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use Hyperf\Amqp\Message\ProducerMessage;
use Hyperf\Amqp\Message\Type;

/**
 * RabbitMQ中一个核心的原则是，消息不能直接投递到Queue中。Producer只能将自己的消息投递到Exchange中，由Exchange按照路由规则将消息投递到对应的Queue中。
 *
 * Class DemoProducer
 * @package App\Amqp\Producer
 */
class DemoProducer extends ProducerMessage
{
    /**
     * 交换机类型
     * ----------------------------------------------------------------------------------------------------------------------------------------------------
     * |    self::DIRECT |  直接转发路由，其实现原理是会将消息中的RoutingKey与该Exchange关联的所有Binding中的BindingKey进行比较，如果相等，则发送到该Binding对应的Queue中。
     * ----------------------------------------------------------------------------------------------------------------------------------------------------
     * |    self::FANOUT |  复制分发路由，该路由不需要RoutingKey，会将消息发送给所有与该 Exchange 定义过Binding的所有Queues中去，其实是一种广播行为。
     * ----------------------------------------------------------------------------------------------------------------------------------------------------
     * |    self::TOPIC  |  通配路由，是direct exchange的通配符模式，消息中的RoutingKey可以写成通配的模式，exchange支持“#”和“*” 的通配。收到消息后，将消息转发给所有符合匹配正则表达式的Queue。
     * |                 |  #：匹配多个词 *： 匹配一个词
     * ----------------------------------------------------------------------------------------------------------------------------------------------------
     * @var string
     */
    public $type = Type::FANOUT;

    /**
     * 交换机名称
     *
     * @var string
     */
    protected $exchange = "im.message.fanout";

    /**
     * 路由key(exchange根据这个Routing Key进行消息投递到队列queue。)
     * @var string
     */
    protected $routingKey = "consumer:im:message";

    /**
     * 生产队列消息
     * DemoProducer constructor.
     * @param $data
     */
    public function __construct($data)
    {
        var_dump("生产队列消息数据：" . $data);
        $this->payload = $data;
    }
}
