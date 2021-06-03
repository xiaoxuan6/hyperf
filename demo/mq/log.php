<?php
/**
 * 发布/订阅 （扇型交换机,它能做的仅仅是广播）
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/3
 * Time: 11:52
 */

/**
 * @var $connection PhpAmqpLib\Connection\AMQPStreamConnection
 * @var $channel PhpAmqpLib\Channel\AMQPChannel
 */
list($connection, $channel) = require_once "amqp.php";

/**
 * 这里只声明交换机，没有声明队列名，也没有将交换机和队列名进行绑定（队列名为空，会随机生成）
 * 注：这里没有绑定队列到交换器，消息将会丢失。如果没有消费者监听，那么消息就会被忽略。
 */
$channel->exchange_declare("log", \Hyperf\Amqp\Message\Type::FANOUT, false, false, false);

$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "info: Hello World!";
$message = new \PhpAmqpLib\Message\AMQPMessage($data);

$channel->basic_publish($message, "log");

echo " [x] Sent ", $data, "\n";

$channel->close();
$connection->close();

