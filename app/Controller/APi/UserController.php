<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/7
 * Time: 10:16
 */

namespace App\Controller\APi;

use App\Middleware\WebsocketAuthMiddleware;
use App\Model\Oauth;
use App\Model\UserFriend;
use App\Services\MessageHandleService;
use App\Services\SocketClientService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * Class UserController
 * @package App\Controller\APi
 * @AutoController(prefix="api/user")
 */
class UserController extends AbstractController
{
    /**
     * @Inject()
     * @var StdoutLoggerInterface
     */
    protected $log;

    /**
     * @Inject()
     * @var SocketClientService
     */
    protected $socketClientService;

    protected $messageHandleService;

    public function __construct(MessageHandleService $messageHandleService)
    {
        $this->messageHandleService = $messageHandleService;
    }

    /**
     * Notes:
     * Date: 2021/6/7 10:34
     * @param RequestInterface $request
     * @Middleware(WebsocketAuthMiddleware::class)
     */
    public function apply(RequestInterface $request)
    {
        $friendId = trim($request->input("friendId"));

        if ($user = Oauth::query()->where("name", $friendId)->first()) {
            $isFriend = 0;

            if ($userFriend = UserFriend::query()->where([
                ["oauth_id", "=", $request->getAttribute("uid")],
                ["user_id", "=", $user->id]
            ])->orWhere([
                ["oauth_id", "=", $user->id],
                ["user_id", "=", $request->getAttribute("uid")]
            ])->first()) {

                if ($userFriend->status == 0) {
                    $isFriend = 1;
                } elseif ($userFriend->status == 1) {
                    $isFriend = 2;
                }

            }

            return $this->outResponse(0, ["id" => $user->id, "name" => $user->name, "isfriend" => $isFriend]);
        } else {
            return $this->outResponse(__LINE__, "没有搜索到相关结果");
        }
    }

    /**
     * Notes:
     * Date: 2021/6/7 10:34
     * @param RequestInterface $request
     * @Middleware(WebsocketAuthMiddleware::class)
     */
    public function applyFriend(RequestInterface $request)
    {
        $userId = trim($request->input("userId"));
        $uid = $request->getAttribute("uid");

        if (!$user = Oauth::query()->where("id", $userId)->first()) {
            return $this->outResponse(__LINE__, "申请失败，找不到该用户");
        }

        if ($userFriend = UserFriend::query()->where([
            ["oauth_id", "=", $request->getAttribute("uid")],
            ["user_id", "=", $user->id]
        ])->orWhere([
            ["oauth_id", "=", $user->id],
            ["user_id", "=", $request->getAttribute("uid")]
        ])->first()) {

            if ($userFriend->status == 0) {
                return $this->outResponse(0, "已申请成功，无需再次申请");
            } elseif ($userFriend->status = 1) {
                return $this->outResponse(__LINE__, "该用户和您已是好友关系");
            }

        }

        $this->messageHandleService->onConsumeFriendApply($uid, $userId, $request->getAttribute("userInfo"));

        return $this->outResponse(0, "申请成功");
    }

    /**
     * Notes:
     * Date: 2021/6/7 14:52
     * @param RequestInterface $request
     * @Middleware(WebsocketAuthMiddleware::class)
     */
    public function applyFriendAgree(RequestInterface $request)
    {
        $id = $request->input("id");

        if (!$userFriend = UserFriend::query()->whereKey($id)->first()) {
            return $this->outResponse(__LINE__, "申请已过期");
        }

        $userFriend->status = 1;
        $userFriend->save();

        return $this->outResponse(0, "已同意");
    }
}