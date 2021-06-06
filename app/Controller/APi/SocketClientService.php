<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/5
 * Time: 19:48
 */

namespace App\Controller\APi;

use App\Utils\Facade\Redis;

class SocketClientService
{
    /**
     * fd与用户绑定
     */
    const BIND_FD_TO_USER = 'ws:fd:user';

    /**
     * 使用集合做处理（用户可能存在多端登录）
     */
    const BIND_USER_TO_FDS = 'ws:user:fds';


    /**
     * Notes: 客户端fd与用户ID绑定关系
     * Date: 2021/6/5 19:56
     * @param $fd 客户端fd
     * @param $userId 用户id
     */
    public function bindRelation($fd, $userId)
    {
        Redis::zadd(self::BIND_FD_TO_USER, $userId, $fd);
        Redis::sadd(sprintf("%s:%s", self::BIND_USER_TO_FDS, $userId), $fd);
    }

    /**
     * Notes: 查询客户端fd对应的用户ID
     * Date: 2021/6/5 20:03
     * @param int $fd
     * @return mixed
     */
    public function getUserId(int $fd)
    {
        return Redis::zscore(self::BIND_FD_TO_USER, $fd);
    }

    /**
     * Notes: 查询用户对应客户端的（多个）fd
     * Date: 2021/6/6 14:20
     * @param $userId
     * @return array
     */
    public function findFdByUserId($userId)
    {
        return array_map(function ($fd) {
                return (int)$fd;
            }, Redis::smembers(sprintf("%s:%s", self::BIND_USER_TO_FDS, $userId))) ?? [];
    }

    /**
     * Notes: 解除指定的客户端fd与用户绑定关系
     * Date: 2021/6/5 20:05
     * @param int $fd
     * @return mixed
     */
    public function removeRelation(int $fd)
    {
        $userId = $this->getUserId($fd);

        Redis::zrem(self::BIND_FD_TO_USER, $fd);
        Redis::srem(sprintf("%s:%s", self::BIND_USER_TO_FDS, $userId), $fd);
    }

}