<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/5/20
 * Time: 10:26
 */

require_once "../../vendor/autoload.php";
require_once "../autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = AMQP_CONDIG;

$queueName = "task"; // 队列名称
$routingKey = "msg"; // 路由关键字（可以省略）
$exchangeName = "demo:mq"; // 交换机名称

$connection = new AMQPStreamConnection($config["host"], $config["port"], $config["user"], $config["password"]);
$channel = $connection->channel();

$channel->exchange_declare($exchangeName, \Hyperf\Amqp\Message\Type::DIRECT, false, true, false);

$channel->queue_declare($queueName, false, false, false, false);

$channel->queue_bind($queueName, $exchangeName, $routingKey);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

$callabck = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.') ?? 2);
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    // 一个很容易犯的错误就是忘了basic_ack，后果很严重。消息在你的程序退出之后就会重新发送，如果它不能够释放没响应的消息，RabbitMQ就会占用越来越多的内存。
};

#翻译时注：只有consumer已经处理并确认了上一条message时queue才分派新的message给它
// 设置客户端最多接收示被ack的消息个数
$channel->basic_qos(null, 1, null);
/**
 * prefetch_size：最大 unacked 消息的字节数；
 * prefetch_count：最大 unacked 消息的条数；
 * global：上述限制的限定对象，false=限制单个消费者；true=限制整个信道
 */

//$channel->basic_consume($queueName, "", false, true, false, false, $callabck);
// 开启消息确认
$channel->basic_consume($queueName, "", false, false, false, false, $callabck);
// 运行上面的代码，我们发现即使使用CTRL+C杀掉了一个工作者（worker）进程，消息也不会丢失。当工作者（worker）挂掉这后，所有没有响应的消息都会重新发送。

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();