<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/8
 * Time: 10:16
 */

namespace App\Services;

use App\Utils\Facade\Redis;

class UserChatListHandleService
{
    const FRIEND_CHAT_LIST = "friend_chat_list";

    private static function key($receive, $sender)
    {
        return $receive > $sender ? "{$sender}:{$receive}" : "{$receive}:{$sender}";
    }

    /**
     * Notes: 获取好友之间最后一条消息缓存
     * Date: 2021/6/8 10:23
     * @param $receive 接收者ID
     * @param $sender 发送者ID
     * @return mixed
     */
    public function get($receive, $sender)
    {
        return Redis::hget(self::FRIEND_CHAT_LIST, self::key($receive, $sender));
    }

    /**
     * Notes: 设置好友之间最后一条消息记录缓存
     * Date: 2021/6/8 10:22
     * @param $receive 接收者ID
     * @param $sender 发送者ID
     * @param $data 内容
     * @return mixed
     */
    public function set($receive, $sender, $data)
    {
        return Redis::hset(self::FRIEND_CHAT_LIST, self::key($receive, $sender), json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}