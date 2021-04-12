<?php

declare(strict_types=1);

namespace App\Controller;

use App\Utils\Facade\Redis;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class RedisController
{
    public function all()
    {
        $result = Redis::keys("*");

        return success($result);
    }

    public function scan()
    {
        $i = 0;
        $result = [];
        $iterator = null;

        while (true) {
            $res = Redis::scan($iterator, "c*", 10);

            if (!$res || $i++ > 2) {
                break;
            }

            $result = array_merge($result, $res);
        }

        return success($result);
    }

    public function multi()
    {
        $res = Redis::multi(\Redis::MULTI)->set("test1", 1)->get("test1")->exec();

        return success($res);
    }
}
