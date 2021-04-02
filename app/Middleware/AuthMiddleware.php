<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\FooException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\AuthManager;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var AuthManager
     */
    protected $auth;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->auth->guard()->check()) {
            throw new FooException("无效的token");
        }

        // 获取登录的用户信息
        $user = $this->auth->guard()->user();

        $request = Context::override(ServerRequestInterface::class, function () use ($request, $user) {
            return $request->withAttribute("user", $user)->withAttribute("user_id", $user->getId());
        });

        return $handler->handle($request);
    }
}