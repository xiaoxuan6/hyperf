<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/5/19
 * Time: 15:22
 */

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

$config = include BASE_PATH . "/config/autoload/amqp.php";

define("AMQP_CONDIG", $config["default"]);