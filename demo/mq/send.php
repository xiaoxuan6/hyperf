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
use PhpAmqpLib\Message\AMQPMessage;

$config = AMQP_CONDIG;

$queueName = "mq"; // 队列名称
$routingKey = "msg"; // 路由关键字（可以省略）
$exchangeName = "demo:mq"; // 交换机名称

// 建立一个到RabbitMQ服务器的连接
$connection = new AMQPStreamConnection($config["host"], $config["port"], $config["user"], $config["password"], $config["vhost"]);
// 在已连接基础上建立生产者与mq之间的通道
$channel = $connection->channel();

// 声明初始化交换机（type:direct【精准推送】、fanout【广播。推送到绑定到此交换机下的所有队列】、topic【组播。比如上面我绑定的关键字是sms_send，那么他可以推送到*.sms_send的所有队列】）
$channel->exchange_declare($exchangeName, 'direct', false, true, false);
/**
 * passive: false 、是否检测同名队列
 * durable: false、是否开启队列持久化
 *auto_delete: true、通道关闭后是否删除队列
 */

// 声明一个队列
$channel->queue_declare($queueName, false, true, false, false);
/**
 * exclusive: false、队列是否可以被其他队列访问
 */

// 将队列与某个交换机进行绑定，并使用路由关键字
$channel->queue_bind($queueName, $exchangeName, $routingKey);
/**
 * 1、$routingKey其实是可以省略的，但是一般都带上方便交换机对消息进行不同队列的推送
 * 2、如果绑定的时候使用了$routingKey,那么在bashic_publish的时候也要指定$routingKey，不然交换机无法路由到指定队列，默认就推送到不使用关键字的队列了(这在我实验的时候遇到的一个坑)
 * 3、上面的exchange_declare和queue_declare以及queue_bind其实也不是必须的，如果在代码运行之前这行交换机和队列名称以及通过管理后台的方式手动添加在mq上，那么可以执行使用，而不需要上面的这3句代码。
 */

// 向队列发布消息
$message = new AMQPMessage("这是生产消息");

// 推送消息到某个交换机
$channel->basic_publish($message, $exchangeName, $routingKey); // 第一个参数：发送内容，第二个参数：交换机名称， 第三个参数：路由（队列）

echo " [x] Sent '这是生产消息!'\n";

// 关闭通道和连接;
$channel->close();
$connection->close();