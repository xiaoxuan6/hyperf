<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/5/19
 * Time: 11:36
 */

require_once "../../vendor/autoload.php";
require_once "../autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

$queueName = "mq";
$routingKey = "msg"; // 可以省略
$exchangeName = "demo:mq";

$config = AMQP_CONDIG;

// 建立一个到RabbitMQ服务器的连接
$connection = new AMQPStreamConnection($config["host"], $config["port"], $config["user"], $config["password"], $config["vhost"]);
$channel = $connection->channel();

// 声明初始化交换机（type:direct【精准推送】、fanout【广播。推送到绑定到此交换机下的所有队列】、topic【组播。比如上面我绑定的关键字是sms_send，那么他可以推送到*.sms_send的所有队列】）
$channel->exchange_declare($exchangeName, 'direct', false, true, false);

// 声明一个队列
$channel->queue_declare($queueName, false, true, false, false);

// 将队列与某个交换机进行绑定，并使用路由关键字
$channel->queue_bind($queueName, $exchangeName, $routingKey);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

// 接收服务器发送的消息。请记住，消息是从服务器异步发送到客户端的。
$callback = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

// 开始消费队列数据
$channel->basic_consume($queueName, '', false, true, false, false, $callback);
/**
 * @url http://www.iamlintao.com/6419.html
 * queue：消息要取得消息的队列名
 * consumer_tag：消费者标签
 * no_local：这个功能属于AMQP的标准,但是rabbitMQ并没有做实现.
 * no_ack：收到消息后,是否不需要回复确认即被认为被消费
 * exclusive：排他消费者,即这个队列只能由一个消费者消费.适用于任务不允许进行并发处理的情况下.比如系统对接
 * nowait：不返回执行结果,但是如果排他开启的话,则必须需要等待结果的,如果两个一起开就会报错
 * callback：回调函数
 */

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();