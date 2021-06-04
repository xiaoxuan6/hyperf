<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use App\Utils\Facade\Log;
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

        $server = server();
        $server->exist($data["fd"]) && $server->push($data["fd"], $data["data"]);

        return Result::ACK;
    }
}
