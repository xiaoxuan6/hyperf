<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/6/5
 * Time: 20:20
 */

namespace App\Exception;

class AuthException extends \RuntimeException
{
    public function __construct($message = "", $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous = null);
    }

}