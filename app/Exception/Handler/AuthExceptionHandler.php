<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/5
 * Time: 20:19
 */

namespace App\Exception\Handler;

use App\Exception\AuthException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AuthExceptionHandler extends ExceptionHandler
{

    /**
     * Handle the exception, and return the specified result.
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        return response()->json(["code" => $throwable->getCode(), "msg" => $throwable->getMessage()]);
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
        return $throwable instanceof AuthException;
    }
}