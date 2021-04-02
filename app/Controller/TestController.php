<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * @Controller(prefix="/test/demo")
 */
// prefix 表示该 Controller 下的所有方法路由的前缀，默认为类名的小写，如 UserController 则 prefix 默认为 user，如类内某一方法的 path 为 index，则最终路由为 /user/index。
// 注意：prefix 并非一直有效，当类内的方法的 path 以 / 开头时，则表明路径从 URI 头部开始定义，也就意味着会忽略 prefix 的值。
class TestController
{
    /**
     * @RequestMapping(path="index")
     */
    // Hyperf 会自动为此方法生成一个 /test/index 的路由，允许通过 GET 或 POST 方式请求
    // 注意：path 里面的值沒有 '/'，如果有 '/' 变成和下面一样效果
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    /**
     * @RequestMapping(path="/user", methods="get")
     */
    // 注意：path 里面的值必须有前面 '/'，否则保存路由不存在
    public function user(RequestInterface $request)
    {
        $id = $request->input("id", 1);

        return $id;
    }
}
