<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Middleware\WebsocketAuthMiddleware;
use App\Model\ChatList;
use App\Model\Oauth;
use App\Model\UserFriend;
use App\Utils\Facade\Log;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Arr;
use Hyperf\View\RenderInterface;

/**
 * Class HomeController
 * @package App\Controller\Api
 * @AutoController(prefix="api/home")
 */
class HomeController extends AbstractController
{
    /**
     * @Inject()
     * @var StdoutLoggerInterface
     */
    protected $log;

    public function index(RenderInterface $render)
    {
        return $render->render("chat.login");
    }

    /**
     * Notes:
     * Date: 2021/6/6 17:04
     * @param RenderInterface $render
     * @return \Psr\Http\Message\ResponseInterface
     * @Middleware(WebsocketAuthMiddleware::class)
     */
    public function chat(RenderInterface $render, RequestInterface $request)
    {
        // 当前用户信息
        $userInfo = $request->getAttribute("userInfo");

        // 好友列表
        $oauthIds = UserFriend::query()->select("oauth_id as uid")->where("user_id", $userInfo->id)->where("status", 1)->union(
            UserFriend::query()->select("user_id as uid")->where("oauth_id", $userInfo->id)->where("status", 1)
        )->get();

        $userFriend = Oauth::query()->select(["id", "name"])->whereIn("id", $oauthIds->pluck("uid")->toArray())->get();

        $applyUser = UserFriend::query()->where("user_id", $request->getAttribute("uid"))->with("oauth")->orderBy("id", "desc")->limit(5)->get();

        return $render->render("chat.index", compact("userInfo", "userFriend", "applyUser"));
    }

    public function chatRecordList(RequestInterface $request)
    {
        $token = $request->input("token");
        $receive_id = $request->input("receive_id");

        $uid = $this->auth->guard()->user($token)->getId();

        $chatList = ChatList::query()->where(function ($query) use ($uid, $receive_id) {
            $query->where([
                ["oauth_id", "=", $uid],
                ["receive_id", "=", $receive_id],
            ])->orwhere([
                ["oauth_id", "=", $receive_id],
                ["receive_id", "=", $uid],
            ]);
        })->orderBy("id", "desc")->limit(5)->get();

        $userInfos = Oauth::query()->select(["id", "name", "created_at"])->whereIn("id", [$uid, $receive_id])->get()->keyBy("id");

        foreach ($chatList as &$value) {
            $value->uid_name = Arr::get($userInfos->get($value->oauth_id), "name");
            $value->receive_name = Arr::get($userInfos->get($value->receive_id), "name");
            $value->ismember = $value->oauth_id == $uid ? 1 : 0;
        }

        return response()->json(["code" => 200, "data" => $chatList ?? []]);
    }
}
