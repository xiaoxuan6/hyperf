<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Producer\ChatMessageProducer;
use Hyperf\Amqp\Producer;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Swoole\Http\Request;
use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;

class WebsocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    public function onMessage($server, Frame $frame): void
    {
        $producer = app(Producer::class);

        $producer->produce(new ChatMessageProducer($frame->fd,"我是来自[服务器的消息]，{$frame->data}"));
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        echo PHP_EOL."FD : 【{$fd}】 已断开...";
    }

    public function onOpen($server, Request $request): void
    {
        $server->push($request->fd, "成功连接,IM 服务器");
        echo PHP_EOL."FD : 【{$request->fd}】 成功连接...";
    }
}
