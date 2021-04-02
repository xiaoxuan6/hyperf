<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/30
 * Time: 17:10
 */

namespace App\Services;


use App\Job\FooJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class JobService
{
    /**
     * @var \Hyperf\AsyncQueue\Driver\DriverInterface
     */
    protected $driver;

    public function __construct(DriverFactory $driver)
    {
        $this->driver = $driver->get("default");
    }

    /*
     * 生产消息.
     * @param $params 数据
     * @param int $delay 延时时间 单位秒
     */
    public function push($params, int $delay = 0)
    {
        // 这里的 `FooJob` 会被序列化存到 Redis 中，所以内部变量最好只传入普通数据
        // 同理，如果内部使用了注解 @Value 会把对应对象一起序列化，导致消息体变大。
        // 所以这里也不推荐使用 `make` 方法来创建 `Job` 对象。
        $this->driver->push(new FooJob($params), $delay);
    }
}