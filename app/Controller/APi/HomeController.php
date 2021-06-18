<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Middleware\WebsocketAuthMiddleware;
use App\Model\ChatList;
use App\Model\Oauth;
use App\Model\UserChatList;
use App\Model\UserFriend;
use App\Services\UserChatListHandleService;
use Carbon\Carbon;
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

    /**
     * @Inject()
     * @var UserChatListHandleService
     */
    protected $userChatListHandleService;

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

        // 好友聊天列表
        $userChatList = UserChatList::query()->where("uid", $userInfo->getKey())->with(["friendUserInfo"])->get()->map(function (&$userChatList) {

            $data = $this->userChatListHandleService->get($userChatList->uid, $userChatList->friend_id);

            $userChatList->content = $data ? json_decode($data, true)["content"] : "";
            $userChatList->updated_at = $data ? json_decode($data, true)["updated_at"] : $userChatList->updated_at;

            return $userChatList;
        })->toArray();

        array_multisort(array_column($userChatList, "updated_at"), SORT_DESC, $userChatList);

        // 好友列表
        $oauthIds = UserFriend::query()->select("oauth_id as uid")->where("user_id", $userInfo->id)->where("status", 1)->union(
            UserFriend::query()->select("user_id as uid")->where("oauth_id", $userInfo->id)->where("status", 1)
        )->get();

        $userFriend = Oauth::query()->select(["id", "name"])->whereIn("id", $oauthIds->pluck("uid")->toArray())->get();

        return $render->render("chat.index", compact("userInfo", "userChatList", "userFriend"));
    }

    /**
     * Notes: 获取好友之间的聊天记录
     * Date: 2021/6/8 14:24
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function chatRecordList(RequestInterface $request)
    {
        $uid = $this->uid();
        $receive_id = $request->input("receive_id");

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

    /**
     * Notes: 添加对话列表
     * Date: 2021/6/8 14:24
     * @param RequestInterface $request
     */
    public function addTalk(RequestInterface $request)
    {
        $uid = $this->uid();
        $receive_id = $request->input("receive_id");

        UserChatList::query()->updateOrCreate([
            "uid"       => $uid,
            "friend_id" => $receive_id,
        ],[
            "updated_at" => Carbon::now()->toDateTimeString()
        ]);

        return $this->outResponse(0, "ok");
    }

    /**
     * Notes: 对话列表
     * Date: 2021/6/8 14:31
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function userChatList(RequestInterface $request)
    {
        $userChatList = UserChatList::query()->where("uid", $this->uid())->with(["friendUserInfo"])->get()->map(function (&$userChatList) {

            $data = $this->userChatListHandleService->get($userChatList->uid, $userChatList->friend_id);

            $userChatList->content = $data ? json_decode($data, true)["content"] : "";
            $userChatList->updated_at = $data ? json_decode($data, true)["updated_at"] : $userChatList->updated_at;

            return $userChatList;
        })->toArray();

//        array_multisort(array_column($userChatList, "updated_at"), SORT_DESC, $userChatList);
        $userChatList = arraySort($userChatList, "updated_at");

        return $this->outResponse(0, compact("userChatList"));
    }
}
