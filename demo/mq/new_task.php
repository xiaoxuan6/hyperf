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
// delivery_mode：用于做消息持久化（delivery_mode=2）;

    $channel->basic_publish($message, $exchangeName, $routingKey);

    echo ' [x] Sent ', $body, "\n";
}

$channel->close();
$connection->close();

