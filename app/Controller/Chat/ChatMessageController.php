<?php

declare(strict_types=1);

namespace App\Controller\Chat;

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
        return $render->render("welcome");
    }
}
