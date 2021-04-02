<?php

declare(strict_types=1);

namespace App\Controller;

use App\Job\FooJob;
use App\Services\JobService;
use App\Utils\Facade\AsyncQueue;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class JobController
{
    /**
     * @Inject()
     * @var JobService
     */
    protected $service;

    public function index()
    {
//        $this->service->push(__METHOD__, 5);

        // 使用助手函数
        queue(new FooJob(__METHOD__ . "_1"), 2);

        AsyncQueue::push(new FooJob(__METHOD__ . "_2"), 1);
        return 1;
    }
}
