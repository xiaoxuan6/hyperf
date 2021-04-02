<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CoreMiddleware implements MiddlewareInterface
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
        /*if(strtolower($request->getMethod()) == "get") {
            // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
            $log = $this->container->get(LoggerFactory::class)->get("logger");
            $log->info("这是 get 请求：" . $request->getUri());
        }*/

        return $handler->handle($request);
    }
}