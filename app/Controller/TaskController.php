<?php

declare(strict_types=1);

namespace App\Controller;

use App\Task\FooTask;
use App\Task\InjectTask;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Task\Task;
use Hyperf\Task\TaskExecutor;

/**
 * @AutoController()
 */
class TaskController
{
    /**
     * Notes: 主动方法投递
     * Date: 2021/3/31 14:24
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(ResponseInterface $response)
    {
        $task = app(TaskExecutor::class);
        $result = $task->execute(new Task([FooTask::class, "handle"], [__METHOD__]));

        return $response->raw($result);
    }

    /**
     * Notes: 使用注解
     * Date: 2021/3/31 14:28
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function injectTask(ResponseInterface $response)
    {
        $result = app(InjectTask::class)->handle(__METHOD__);

        return $response->raw($result);
    }
}
