<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/31
 * Time: 14:25
 */

namespace App\Task;

use App\Utils\Facade\Log;
use Hyperf\Task\Annotation\Task;
use Hyperf\Utils\Coroutine;

class InjectTask
{
    /**
     * @Task()
     */
    // 注解时需 use Hyperf\Task\Annotation\Task;
    public function handle($params)
    {
        $coId = Coroutine::id();

        Log::info(__METHOD__ . " this is FooTask", [$params] + ["coId" => $coId]);

        return (int)$coId;
    }
}