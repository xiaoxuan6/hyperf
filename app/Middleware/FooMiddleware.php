<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FooMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 只能在中间件中传递，无法在控制器中使用
//        $request = $request->withAttribute("foo", 1);

        // 修改上下文中的 request 对象，这里存放在 attribute 属性中的
        // 从协程上下文取出 $request 对象并设置 key 为 foo 的 Header，然后再保存到协程上下文中
        $request = Context::override(ServerRequestInterface::class, function() use ($request){
            return $request->withAttribute("foo", 1);
        });

        return $handler->handle($request);
    }
}