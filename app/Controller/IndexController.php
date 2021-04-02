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

use App\Utils\Facade\Cache;
use Psr\SimpleCache\CacheInterface;

class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method'  => $method,
            'message' => "Hello {$user}.",
            'action'  => "hyperf"
        ];
    }

    /**
     * Notes: 缓存
     * Date: 2021/3/25 16:54
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cache()
    {
        $cache = $this->container->get(CacheInterface::class);

        $cache->set("saa", 1);

        $cache_value = $cache->get("sd", "default");

        return $this->response->raw($cache_value);
    }

    public function cc()
    {
        Cache::set('aaaa', 1);

        return $this->outResponse(200, ["cache" => Cache::get("aaaa")]);
    }
}
