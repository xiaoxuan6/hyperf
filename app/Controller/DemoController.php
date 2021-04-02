<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\OauthEvent;
use App\Model\Oauth;
use App\Request\OauthRequest;
use App\Utils\Facade\Event;
use App\Utils\Facade\Log;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Paginator\Paginator;
use Psr\EventDispatcher\EventDispatcherInterface;

class DemoController extends AbstractController
{
    /**
     * Notes: 手动分页
     * Date: 2021/3/26 15:56
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index1()
    {
        $page = $this->request->input("page", 1);
        $pageSize = $this->request->input("pageSize", 2);

        $oauth = Oauth::query()->skip(($page - 1) * $pageSize)->take($pageSize)->get();

        return $this->outResponse(200, $oauth);
    }

    /**
     * Notes: 使用分页器 paginator
     * Date: 2021/3/26 15:55
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index2()
    {
//        $page = (int)$this->request->input("page", 1);
//        $pageSize = (int)$this->request->input("pageSize", 2);
        $page = $this->int("page", 1);
        $pageSize = $this->int("pageSize", 2);

        $oauth = Oauth::query()->skip(($page - 1) * $pageSize)->take($pageSize)->get();

        $data = new Paginator($oauth, $pageSize, $page, ["path" => $this->request->url()]);

        return $this->outResponse(200, $data);
    }

    public function index()
    {
        $age = $this->int("age", 10);
        $pageSize = $this->int("pageSize", 2);

        $oauth = Oauth::query()->where("age", $age)->paginate($pageSize);

        return $this->outResponse(200, $oauth);
    }

    /**
     * Notes: 新增
     * Date: 2021/3/26 15:56
     * @param OauthRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function store(OauthRequest $request)
    {
        if (!$request->validated()) {
            return $this->outResponse($request->messages());
        }

        $oauth = Oauth::query()->create(["name" => $request->input("name"), "age" => $request->input("age")]);

//        $this->container->get(EventDispatcherInterface::class)->dispatch(new OauthEvent($oauth));
        // or
//        $this->eventDispatcher->dispatch(new OauthEvent($oauth));
        // or
        Event::dispatch(new OauthEvent($oauth));
//        event(new OauthEvent($oauth));

        return $this->outResponse(200, "添加成功");
    }

    /*
     * @Inject()
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Notes: 修改
     * Date: 2021/3/26 15:56
     * @param OauthRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(OauthRequest $request)
    {
        if (!$request->validated()) {
            return $this->outResponse($request->messages());
        }

        if (!$id = $this->request->input("id")) {
            return $this->outResponse(__LINE__, "无效的参数：ID");
        }

        $oauth = Oauth::query()->whereKey($id)->first();
        $oauth->name = $request->input("name");
        $oauth->age = $request->input("age");
        $oauth->save();

        return $this->outResponse([0, "修改成功"]);
    }

    /**
     * Notes: 删除
     * Date: 2021/3/26 17:26
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete()
    {
        if (!$id = $this->request->input("id")) {
            return $this->outResponse(__LINE__, "无效的参数：ID");
        }

        Oauth::query()->whereKey($id)->delete();

        return $this->outResponse(0, "删除成功");
    }
}
