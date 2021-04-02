<?php

declare(strict_types=1);

namespace App\Controller;

use App\Utils\Facade\Log;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 */
class LogController
{
    public function index()
    {
        Log::info(__METHOD__ . " 这是 => foojob 队列");
        Log::setName("log122112122")->error(__METHOD__);
//        Log::error(__METHOD__ . "报错了");
//        Log::info(__METHOD__ . " ----------");
//        Log::error(__METHOD__ . "报错了9999999999999");

        return 111 . __METHOD__;
    }
}
