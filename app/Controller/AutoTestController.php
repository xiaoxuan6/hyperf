<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class AutoTestController
{
    /*
     * Notes: 注解路由：Hyperf 会自动为此方法生成一个 /auto_test/index 的路由，允许通过 GET 或 POST 方式请求
     * Warn: 如果方法名是驼峰这不需要使用 '_' 访问，EX：/auto_test/indexList
     *
     * @url http://127.0.0.1:9501/auto_test/index
     * Date: 2021/3/25 17:03
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello AutoTestController Hyperf!');
    }

    public function indexList()
    {
        return response()->json(["code" => 200, "msg" => __METHOD__]);
    }
}
