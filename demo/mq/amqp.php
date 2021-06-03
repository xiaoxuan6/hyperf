<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/3
 * Time: 15:52
 */

require_once "../../vendor/autoload.php";
require_once "../autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = AMQP_CONDIG;

$connection = new AMQPStreamConnection($config["host"], $config["port"], $config["user"], $config["password"]);
$channel = $connection->channel();

return [$connection, $channel];