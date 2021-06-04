<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Producer\ChatMessageProducer;
use App\Constants\Commen;
use App\Utils\Facade\Log;
use App\Utils\Facade\Redis;
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

        // 获取fd绑定的用户
        $userId = Redis::hget(Commen::BIND_FD_TO_USER, (string)$frame->fd) ?? $frame->fd;

        $data = json_decode($frame->data, true);
        $producer->produce(new ChatMessageProducer($userId, $frame->fd, $data));
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        echo PHP_EOL . "FD : 【{$fd}】 已断开...";
        $userId = Redis::hget(Commen::BIND_FD_TO_USER, (string)$fd);

        // 删除fd绑定的数据
        Redis::hdel(Commen::BIND_FD_TO_USER, (string)$fd);
        Redis::srem(sprintf('%s:%s', Commen::BIND_USER_TO_FDS, $userId), (string)$fd);
    }

    public function onOpen($server, Request $request): void
    {
        $userId = $request->get["id"] ?? 0;

        echo PHP_EOL . "FD : 【{$userId}】 成功连接...";

        // 绑定fd与用户关系
        Redis::hset(Commen::BIND_FD_TO_USER, (string)$request->fd, $userId);
        // 将用户和fd放在集合中，便于发送消息的时候获取
        Redis::sadd(sprintf('%s:%s', Commen::BIND_USER_TO_FDS, $userId), $request->fd);

    }
}
