<?php

declare(strict_types=1);

namespace App\Controller;

use App\Utils\Facade\Config;
use App\Utils\Facade\Redis;
use Hyperf\Config\Annotation\Value;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class ConfigController extends AbstractController
{
    /*     * 1、使用 config 类获取     */
    /**
     * @Inject()
     * @var ConfigInterface
     */
    private $config;

    public function index()
    {
        // 直接获取 config 配置文件中的 value
//        return $this->outResponse(200, $this->config->get("app_name"));

        // 获取 autoload 文件下的配置信息
        return $this->config->get("cache.default");
    }

    /*     * 2、使用 value 函数获取     */

    /**
     * @Value("cache.default")
     */
    private $foo;

    public function value()
    {
        return $this->foo;
    }


    /*     * 3、使用助手函数 config     */
    public function config()
    {
        return config("cache.default");
    }

    public function aaa()
    {
        return Config::get("cache.default", 'sd');
    }

    public function redis()
    {
        Redis::set(__METHOD__, 1);

        return $this->outResponse(200, ["cache" => Redis::get(__METHOD__)]);
    }

}
