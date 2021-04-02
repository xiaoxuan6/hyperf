<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/31
 * Time: 16:04
 */

namespace App\Utils\Facade;

use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;

abstract class Facade
{
    /**
     * @var
     */
    static public $container;

    /**
     * Notes:
     * Date: 2021/3/31 11:10
     * @param $name
     * @param $arguments
     * @return mixed
     */
    static public function __callStatic($method, $arguments)
    {
        $contain = self::fetchApplication();

        return $contain->{$method}(...$arguments);
    }

    public function __call($method, $arguments)
    {
        $contain = self::fetchApplication();

        return $contain->{$method}(...$arguments);
    }

    /**
     * Notes: 获取已创建的应用
     * Date: 2021/4/1 9:51
     * @return mixed|\Psr\Container\ContainerInterface
     */
    static private function fetchApplication()
    {
        $contain = self::getApplication();

        if ($name = static::$container)
            $contain = $contain->get($name);

        if ($config = static::getDefaultConfig()) {
            $contain = $contain->get($config);
        }

        return $contain;
    }

    /**
     * Notes: 获取默认 name
     * Date: 2021/4/1 9:43
     * @return mixed|null|string
     */
    static private function getDefaultConfig()
    {
        return Context::get("name") ?? static::getConfig();
    }

    /**
     * Notes:
     * Date: 2021/3/31 16:24
     * @return null|string
     */
    static public function getConfig(): ?string
    {
        return null;
    }

    /**
     * Notes:
     * Date: 2021/3/31 16:09
     * @return \Psr\Container\ContainerInterface
     */
    static private function getApplication()
    {
        return ApplicationContext::getContainer();
    }
}