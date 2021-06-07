<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Oauth;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController(prefix="api/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @Inject()
     * @var StdoutLoggerInterface
     */
    protected $log;

    public function login()
    {
        $name = request()->input("name");
        $password = request()->input("password");

        if (!$user = Oauth::login(compact("name", "password")))
            return $this->outResponse(__LINE__, "无效的用户名或密码");

        $token = $this->auth->guard()->login($user);

        return $this->outResponse(200, compact("token"));
    }

    public function logout()
    {
        $this->auth->guard()->logout();

        return $this->outResponse(0, "退出成功");
    }
}
