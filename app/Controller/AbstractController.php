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

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    public function outResponse($code, $msg = '')
    {
        if (is_array($code)) {
            list($code, $msg) = $code;
        }

        if (!is_string($msg)) {
            $json = ["code" => $code, "data" => $msg];
        } else {
            $json = ["code" => $code, "msg" => $msg];
        }

        return $this->response->json($json);
    }

    /**
     * Notes:
     * Date: 2021/3/26 15:59
     * @param $key
     * @param null $default
     * @return int
     */
    public function int($key, $default = null): int
    {
        return (int)$this->requestOpteration($key, $default);
    }

    /**
     * Notes:
     * Date: 2021/3/26 16:01
     * @param $key
     * @param null $default
     * @return string
     */
    public function string($key, $default = null)
    {
        return (string)$this->requestOpteration($key, $default);
    }

    /**
     * Notes:
     * Date: 2021/3/26 16:00
     * @param $key
     * @param null $default
     * @return mixed
     */
    private function requestOpteration($key, $default = null)
    {
        return $this->request->input($key, $default);
    }
}
