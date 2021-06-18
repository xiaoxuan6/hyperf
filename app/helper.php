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

if (!function_exists("request")) {
    /**
     * Notes:
     * Date: 2021/5/10 18:24
     * @param null $key
     * @param null $default
     * @return mixed|null|\Hyperf\HttpServer\Contract\RequestInterface|string
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app(Hyperf\HttpServer\Contract\RequestInterface::class);
        }

        $value = "";
        if ($inputVal = app(Hyperf\HttpServer\Contract\RequestInterface::class)->input($key)) {
            $value = $inputVal;
        } elseif ($postVal = app(Hyperf\HttpServer\Contract\RequestInterface::class)->post($key)) {
            $value = $postVal;
        } elseif ($headerVal = app(Hyperf\HttpServer\Contract\RequestInterface::class)->header($key)) {
            $value = $headerVal;
        } elseif ($attributeVal = app(Hyperf\HttpServer\Contract\RequestInterface::class)->getAttribute($key)) {
            $value = $attributeVal;
        }

        return is_null($value) ? $default : $value;
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

if (!function_exists("success")) {
    /**
     * Notes:
     * Date: 2021/4/9 17:21
     * @param $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    function success($data)
    {
        return response()->json(["code" => 200, "data" => $data]);
    }
}

if (!function_exists("fail")) {
    /**
     * Notes:
     * Date: 2021/4/9 17:21
     * @param $data
     * @param int $code
     * @return \Psr\Http\Message\ResponseInterface
     */
    function fail($data, $code = __LINE__)
    {
        return response()->json(["code" => $code, "data" => $data]);
    }
}

/**
 * Notes: Server 实例 基于 Swoole Server
 * Date: 2021/5/18 17:45
 * @return \Swoole\Coroutine\Server|\Swoole\Server
 */
function server()
{
    return app()->get(\Hyperf\Server\ServerFactory::class)->getServer()->getServer();
}

function arraySort(array $array = [], $column, $sort = SORT_DESC)
{
    array_multisort(array_column($array, $column), $sort, $array);

    return $array;
}