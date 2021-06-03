<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/5/20
 * Time: 10:16
 */

require_once "../../vendor/autoload.php";
require_once "../autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = AMQP_CONDIG;

$queueName = "task"; // 队列名称
$routingKey = "msg"; // 路由关键字（可以省略）
$exchangeName = "demo:mq"; // 交换机名称

$connection = new AMQPStreamConnection($config["host"], $config["port"], $config["user"], $config["password"]);
$channel = $connection->channel();

$channel->exchange_declare($exchangeName, \Hyperf\Amqp\Message\Type::DIRECT, false, true, false);

$channel->queue_declare($queueName, false, false, false, false);

$channel->queue_bind($queueName, $exchangeName, $routingKey);

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) $data = "Hello World!";

for ($i = 0; $i < 10; $i++) {
    $body = $i . $data . rand(0, 999);
    $message = new AMQPMessage($body, [
        "delivery_mode" => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);
/**
 * delivery_mode：用于做消息持久化（delivery_mode=2）;
 *
 * 将消息设为持久化并不能完全保证不会丢失。以上代码只是告诉了RabbitMq要把消息存到硬盘，但从RabbitMq收到消息到保存之间还是有一个很小的间隔时间。
 * 因为RabbitMq并不是所有的消息都使用fsync(2)——它有可能只是保存到缓存中，并不一定会写到硬盘中。并不能保证真正的持久化，但已经足够应付我们的简单工作队列。
 * 如果你一定要保证持久化，你可以使用publisher confirms。
 */

    $channel->basic_publish($message, $exchangeName, $routingKey);

    echo ' [x] Sent ', $body, "\n";
}

$channel->close();
$connection->close();

