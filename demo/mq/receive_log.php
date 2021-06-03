<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/3
 * Time: 13:49
 */

require_once "../../vendor/autoload.php";
require_once "../autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = AMQP_CONDIG;

$connection = new AMQPStreamConnection($config["host"], $config["port"], $config["user"], $config["password"]);
$channel = $connection->channel();

$channel->exchange_declare("log", \Hyperf\Amqp\Message\Type::FANOUT, false, false, false);

/**
 * 生产者没有初始化队列名并绑定交换机，消费者初始化并生成随机队列名
 */
list($queue_name,,) = $channel->queue_declare("", false, false, false, false);
echo ' [x] queue name :', $queue_name, "\n";

$channel->queue_bind($queue_name, "log");

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function($msg){
    echo ' [x] ', $msg->body, "\n";
};

$channel->basic_consume($queue_name, "", false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

