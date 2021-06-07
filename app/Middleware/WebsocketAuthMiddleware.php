<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\AuthException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\AuthManager;

class WebsocketAuthMiddleware implements MiddlewareInterface
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
        $token = $request->getAttribute("token") ?? request()->input("token");

        if (!$token || !$this->auth->check($token)) {
            throw new AuthException("无效的token");
        }

        $request = $this->setRequestContext($token);

        return $handler->handle($request);
    }

    protected function setRequestContext($token)
    {
        $userInfo = $this->auth->guard()->user($token);

        $request = Context::get(ServerRequestInterface::class);
        $request = $request->withAttribute("userInfo", $userInfo);
        $request = $request->withAttribute("uid", $userInfo->id);

        Context::set(ServerRequestInterface::class, $request);

        return $request;
    }
}