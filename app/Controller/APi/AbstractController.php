<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Api;

use Hyperf\Di\Annotation\Inject;
use Qbhy\HyperfAuth\AuthManager;

abstract class AbstractController extends \App\Controller\AbstractController
{
    /**
     * @Inject()
     * @var AuthManager
     */
    protected $auth;

    protected function getToken()
    {
        return $token = request()->input("token");
    }

    public function uid($token = null)
    {
        if (is_null($token)) {
            $token = $this->getToken();
        }

        return $this->getUser($token)->getId();
    }

    public function getUser($token = null)
    {
        if (is_null($token)) {
            $token = $this->getToken();
        }
        
        return $this->auth->guard()->user($token);
    }
}
