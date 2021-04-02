<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/3/29
 * Time: 16:20
 */

namespace App\Exception;


class FooException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $code = $code == 0 ? __LINE__ : $code;

        parent::__construct($message, $code, $previous);
    }
}