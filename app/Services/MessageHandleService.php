<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/7
 * Time: 16:01
 */

namespace App\Services;

use App\Model\ChatList;
use App\Model\Oauth;
use App\Model\UserFriend;
use Carbon\Carbon;
use Swoole\WebSocket\Frame;

class MessageHandleService
{
    const ON_TALK_EVENT = "talk_event";

    const ON_FRIEND_APPLY_EVENT = "friend_apply_event";

    protected $socketClientService, $userChatListHandleService;

    public function __construct(SocketClientService $socketClientService, UserChatListHandleService $userChatListHandleService)
    {
        $this->socketClientService = $socketClientService;
        $this->userChatListHandleService = $userChatListHandleService;
    }

    /**
     * Notes: 好友申请消息
     * Date: 2021/6/7 16:07
     * @param $uid 申请者ID
     * @param $userId 接收者ID
     * @param $data 消息内容
     */
    public function onConsumeFriendApply($uid, $userId, $userInfo)
    {
        $userFriend = UserFriend::query()->create([
            "oauth_id" => $uid,
            "user_id"  => $userId,
            "status"   => 0
        ]);

        $data = json_encode(["event" => self::ON_FRIEND_APPLY_EVENT, "name" => $userInfo["name"], "id" => $userFriend->getKey()], JSON_UNESCAPED_UNICODE);

        $fd = $this->socketClientService->findFdByUserId($userId);

        $this->socketPushNotify($fd, $data);
    }

    /**
     * Notes: 对话聊天消息
     * Date: 2021/6/7 16:16
     * @param $fd
     * @param Frame $frame
     * @param $data
     */
    public function onConsumeTalk($fd, Frame $frame, $data)
    {
        // 获取接收者的id
        if (isset($data["receive_user"])) {
            $userId = $data["receive_user"];
            $currentUserId = $this->socketClientService->getUserId($fd);
        } else {
            $currentUserId = $userId = $this->socketClientService->getUserId($fd);
        }

        // 获取当前发送者的用户信息
        $userInfo = Oauth::query()->select(["id", "name"])->whereKey($currentUserId)->first();

        // 获取接收者的fd
        $fds = $this->socketClientService->findFdByUserId($userId);

        $data = isset($data["data"]) ? $data["data"] : $frame->data;

        // 添加聊天记录
        ChatList::saveChatRecord($currentUserId, $userId, $data);

        // 设置最后一条消息缓存
        $this->userChatListHandleService->set($userId, $currentUserId, ["content" => mb_substr($data, 0, 6), "updated_at" => Carbon::now()->toDateTimeString()]);

        $message = json_encode(["event" => self::ON_TALK_EVENT, "data" => $data, "username" => $userInfo->name], JSON_UNESCAPED_UNICODE);

        $this->socketPushNotify($fds, $message);
    }

    /**
     * WebSocket 消息推送
     *
     * @param $fds
     * @param $data
     */
    private function socketPushNotify($fds, $data)
    {
        $data = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;

        $server = server();
        foreach ($fds as $fd) {
            echo $fd;
            $server->exist($fd) && $server->push($fd, $data);
        }
    }


}