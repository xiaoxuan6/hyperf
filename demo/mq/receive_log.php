<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/3
 * Time: 13:49
 */

/**
 * @var $connection PhpAmqpLib\Connection\AMQPStreamConnection
 * @var $channel PhpAmqpLib\Channel\AMQPChannel
 */
list($connection, $channel) = require_once "amqp.php";

$channel->exchange_declare("log", \Hyperf\Amqp\Message\Type::FANOUT, false, false, false);

/**
 * 生产者没有初始化队列名并绑定交换机，消费者初始化并生成随机队列名
 */
list($queue_name,,) = $channel->queue_declare("", false, false, false, false);
echo ' [x] queue name :', $queue_name, "\n";

$channel->queue_bind($queue_name, "log");
/**
 * 绑定时：第三个参数为路由，当交换机类型为：fanout 会被忽略
 */

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

