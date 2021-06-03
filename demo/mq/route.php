<?php
/**
 * 路由（使用直连交换机，扇型交换机 fanout 会被忽略）
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

$channel->exchange_declare(ROUTE_EXCHANGE, \Hyperf\Amqp\Message\Type::DIRECT, false, false, false);

$data = "Hello World!";
$msg = new \PhpAmqpLib\Message\AMQPMessage($data);

$routing_key = ROUTE_KEY[array_rand(ROUTE_KEY, 1)];
$channel->basic_publish($msg, ROUTE_EXCHANGE, $routing_key);

echo " [x] Sent ", $routing_key, ':', $data, " \n";

$channel->close();
$connection->close();
