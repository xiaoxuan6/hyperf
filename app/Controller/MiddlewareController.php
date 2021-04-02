<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\FooException;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\FooMiddleware;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @AutoController()
 * @Middleware(FooMiddleware::class)
 */
class MiddlewareController
{
    public function index(RequestInterface $request)
    {
        // 这里获取中间件中添加的属性
        $fooValue = $request->getAttribute("foo");

        return "index => " . $fooValue;
    }

    public function exception()
    {
        throw new FooException("this is FooException");
    }
}
