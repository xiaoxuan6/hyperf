<?php

declare(strict_types=1);

namespace App\Job;

use App\Utils\Facade\Log;
use Hyperf\AsyncQueue\Job;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;

class FooJob extends Job
{
    /*
     * @Inject()
     * @var LoggerFactory
     */
//    public $logger;
    // 这里不能使用注解方法，如果该变量很大会导致队列序列化失败
    // [ERROR] Serialization of 'Swoole\Http\Server' is not allowed[59] in /mnt/c/Code_cloud/hyperf-skeleton/vendor/hyperf/async-queue/src/Message.php
    // [ERROR] #0 /mnt/c/Code_cloud/hyperf-skeleton/vendor/hyperf/async-queue/src/Message.php(59): serialize()

    public $params;

    /**
     * @var int 最大失败重试次数
     */
    protected $maxAttempts = 2;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function handle()
    {
//        var_dump($this->params);
//        $logger = $this->logger->get("logger");
//        $logger->info(__METHOD__ . " job " . $this->params);

        Log::info(__METHOD__, [$this->params]);
    }
}
