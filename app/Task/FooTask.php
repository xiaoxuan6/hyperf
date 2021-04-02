<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/31
 * Time: 14:09
 */

namespace App\Task;

use App\Utils\Facade\Log;
use Hyperf\Utils\Coroutine;

class FooTask
{
    public function handle($params)
    {
        $coId = Coroutine::id();

        Log::info(__METHOD__ . " this is FooTask", [$params] + ["coId" => $coId]);

        return (int)$coId;
    }
}