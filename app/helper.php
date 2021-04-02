<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/30
 * Time: 18:02
 */

if (!function_exists('app')) {
    /**
     * @param null $class
     * @return mixed|\Psr\Container\ContainerInterface
     */
    function app($class = null)
    {
        $container = \Hyperf\Utils\ApplicationContext::getContainer();

        if (!is_null($class)) {
            return $container->get($class);
        }

        return $container;
    }
}

if (!function_exists("event")) {
    /**
     * Notes:
     * Date: 2021/3/31 16:57
     * @param object $event
     * @return mixed
     */
    function event(object $event)
    {
        return app(\Psr\EventDispatcher\EventDispatcherInterface::class)->dispatch($event);
    }
}

if (!function_exists("queue")) {
    /**
     * Notes: Push a job to async queue.
     * Date: 2021/3/30 18:09
     * @param \Hyperf\AsyncQueue\JobInterface $job
     * @param int $delay
     * @param string $key
     * @return mixed
     */
    function queue(\Hyperf\AsyncQueue\JobInterface $job, $delay = 0, $key = "default")
    {
        $driver = app(\Hyperf\AsyncQueue\Driver\DriverFactory::class)->get($key);

        return $driver->push($job, $delay);
    }
}

if (!function_exists("response")) {
    /**
     * Notes:
     * Date: 2021/4/1 15:55
     * @return mixed|\Hyperf\HttpServer\Contract\ResponseInterface
     */
    function response()
    {
        return app(\Hyperf\HttpServer\Contract\ResponseInterface::class);
    }
}