<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Oauth;
use App\Utils\Facade\Log;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\View\RenderInterface;
use Qbhy\HyperfAuth\AuthManager;
use App\Middleware\AuthMiddleware;

/**
 * @AutoController()
 */
class AuthController extends AbstractController
{
    public function index(RenderInterface $render)
    {
        return $render->render("login");
    }

    /**
     * @Inject()
     * @var AuthManager
     */
    private $auth;

    public function login()
    {
//        $name = $this->request->input("name");
//        $password = $this->request->input("password");

        $name = request()->input("name") ?? request()->input("email");
        $password = request()->input("password");

        if (!$user = Oauth::login(["name" => $name, "password" => $password]))
            return $this->outResponse(__LINE__, "无效的账号密码");

        $token = $this->auth->guard()->login($user);

        $id = $user->id;
        return $this->outResponse(200, compact("id", "token"));
    }

    /**
     * Notes: 检查生成的token是否有效
     * Date: 2021/4/1 13:30
     * @return bool
     */
    public function check()
    {
//        $token = $this->request->header("token");
//        $token = request()->header("token");
        $token = request("token");

        $result = $this->auth->guard()->check($token);

        Log::info(__METHOD__ . " 检测token是否有效", compact("token", "result"));

        return (int)$result;
    }

    /**
     * Notes: 刷新token
     * Date: 2021/4/1 13:31
     * @return mixed
     */
    public function refresh()
    {
        $token = $this->request->input("token");

        $newToken = $this->auth->guard()->refresh($token);

        Log::info(__METHOD__ . " 刷新token", compact("token", "newToken"));

        return $this->outResponse(200, compact("newToken"));
    }

    /**
     * @Middleware(AuthMiddleware::class)
     */
    public function getUserInfo()
    {
        $user = $this->request->getAttribute("user");
        $user_id = $this->request->getAttribute("user_id");

        return $this->outResponse(200, compact("user", "user_id"));
    }

    /**
     * @Middleware(AuthMiddleware::class)
     */
    public function getUser()
    {
        $name = $this->auth->guard()->getName();

        return $this->outResponse(200, compact("name")); // JWT
    }
}
