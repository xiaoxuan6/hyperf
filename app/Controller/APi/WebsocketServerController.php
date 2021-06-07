<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/5
 * Time: 19:36
 */

namespace App\Controller\Api;

use App\Services\MessageHandleService;
use App\Services\SocketClientService;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\AuthManager;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;
use Swoole\WebSocket\Frame;

class WebsocketServerController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    /**
     * @Inject()
     * @var AuthManager
     */
    protected $auth;

    /**
     * @Inject()
     * @var SocketClientService
     */
    protected $socketClientService;

    /**
     * @Inject()
     * @var MessageHandleService
     */
    protected $messageHandleService;

    /**
     * @param Response|Server $server
     */
    public function onClose($server, int $fd, int $reactorId): void
    {
        $userId = $this->socketClientService->getUserId($fd);

        echo PHP_EOL . "客户端FD:{$fd} 已关闭连接 ，用户ID为" . $userId;

        // 删除 fd 绑定关系
        $this->socketClientService->removeRelation($fd);
    }

    /**
     * @param Response|\Swoole\WebSocket\Server $server
     */
    public function onMessage($server, Frame $frame): void
    {
        // 当前客户端fd
        $fd = $frame->fd;
        $data = json_decode($frame->data, true);

        $this->messageHandleService->onConsumeTalk($fd, $frame, $data);
    }

    /**
     * @param Response|\Swoole\WebSocket\Server $server
     */
    public function onOpen($server, Request $request): void
    {
        $token = $request->get["token"] ?? null;

        $userInfo = $this->auth->user($token);
        echo PHP_EOL . "当前用户ID：" . $userInfo->getId() . " | fd：{$request->fd}";

        // 绑定用户和fd的关系
        $this->socketClientService->bindRelation($request->fd, $userInfo->getId());
    }
}