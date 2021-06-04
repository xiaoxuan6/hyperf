<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Constants\Commen;
use App\Utils\Facade\Redis;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;

/**
 * Class ChatMessageController
 * @package App\Controller\Chat
 * @AutoController(prefix="chat/message")
 */
class ChatMessageController
{
    public function index(RenderInterface $render)
    {
        // 获取所有在线用户
        $userids = Redis::hgetAll(Commen::BIND_FD_TO_USER);

        return $render->render("welcome", compact("userids"));
    }
}
