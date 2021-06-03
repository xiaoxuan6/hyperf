<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/3
 * Time: 15:10
 */

require_once "../../vendor/autoload.php";
require_once "../autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = AMQP_CONDIG;

$connection = new AMQPStreamConnection($config["host"], $config["port"], $config["user"], $config["password"]);
$channel = $connection->channel();

$channel->exchange_declare(ROUTE_EXCHANGE, \Hyperf\Amqp\Message\Type::DIRECT, false, false, false);

list($queue_name, ,) = $channel->queue_declare("", false, false, false, false);

$severities = array_slice($argv, 1);
if(empty($severities )) {
    file_put_contents('php://stderr', "Usage: $argv[0] [info] [warning] [error]\n");
    exit(1);
}

foreach ($severities as $severity) {
    $channel->queue_bind($queue_name, ROUTE_EXCHANGE, $severity); // 队列绑定到同一个交换机不同的路由上
}

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
