<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use App\Constants\Commen;
use App\Utils\Facade\Log;
use App\Utils\Facade\Redis;
use Hyperf\Amqp\Message\Type;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * @Consumer(name ="ChatMessageConsumer", nums=1)
 */
class ChatMessageConsumer extends ConsumerMessage
{
    protected $type = Type::DIRECT;

    protected $exchange = "demo:chat:ex";

    protected $routingKey = "demo:chat:routing";

    protected $queue = "demo:chat";

    public function consumeMessage($data, AMQPMessage $message): string
    {
        Log::info(__METHOD__ . " 消费队列", [$data]);

        $fds = Redis::smembers(sprintf('%s:%s', Commen::BIND_USER_TO_FDS, $data["data"]["receive_user"]));
        $fid = $fds ? array_map(function ($fd) {
            return (int)$fd;
        }, $fds) : [];

        Log::info(__METHOD__ . "fd", ["fd" => $fid]);

        $this->socketPushNotify($fid, $data["data"]["data"]);

//        $server = server();
//        $server->exist($fd) && $server->push($fd, $data["data"]["data"]);
//        Log::info(__METHOD__, [$fd, $data["data"]["data"]]);

        return Result::ACK;
    }

    private function socketPushNotify($fds, $message)
    {
        $server = server();
        foreach ($fds as $fd) {
            $server->exist($fd) && $server->push($fd, $message);
        }
    }
}
