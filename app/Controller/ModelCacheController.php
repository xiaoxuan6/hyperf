<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Oauth;
use App\Utils\Facade\Redis;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController()
 */
class ModelCacheController
{
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    public function data()
    {
        $data = Oauth::query()->whereKey(1)->first();

        return response()->json(["code" => 200, "data" => $data]);
    }

    public function cacheData()
    {
        $data = Oauth::findFromCache(1);

        return response()->json(["code" => "200", "data" => $data]);
    }

    public function cache()
    {
        $cache = Redis::hgetAll("mc:default:m:Oauth:id:1");

        return $cache;
    }

    public function delete()
    {
        // 通过全局删除事件监听删除操作
        Oauth::query(true)->whereKey(1)->delete();

    }

    public function notExists()
    {
        Oauth::query()->whereKey(10000)->firstOrFail();

        return response()->raw("sd");
    }
}
