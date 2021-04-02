<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/29
 * Time: 16:21
 */

namespace App\Exception\Handler;


use App\Exception\FooException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use function Swoole\Coroutine\Http\request;
use Throwable;

class FooExceptionHandler extends ExceptionHandler
{

    /**
     * Handle the exception, and return the specified result.
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // TODO: Implement handle() method.
        // 终止异常，不继续执行
        $this->stopPropagation();

        // 返回数组格式
        return response()->json(["code" => $throwable->getCode(), "msg" => $throwable->getMessage()]);

        // 返回 string 类型报错
//        return $response->withStatus(500)->withBody(new SwooleStream($throwable->getMessage()));
    }

    /**
     * Determine if the current exception handler should handle the exception,.
     *
     * @return bool
     *              If return true, then this exception handler will handle the exception,
     *              If return false, then delegate to next handler
     */
    public function isValid(Throwable $throwable): bool
    {
        // TODO: Implement isValid() method.
        return $throwable instanceof FooException;
    }
}