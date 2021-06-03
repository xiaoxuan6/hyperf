<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use App\Utils\Facade\Log;
use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;
use Hyperf\Amqp\Message\Type;

/**
 * @Producer()
 */
class ChatMessageProducer extends ProducerMessage
{
    protected $type = Type::DIRECT;

    protected $exchange = "demo:chat:ex";

    protected $routingKey = "demo:chat:routing";

    public function __construct($data)
    {
        Log::info(__METHOD__ . " 生产队列", [$data]);
        $this->payload = $data;
    }
}
