<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'handler' => [
        // 这里的 http 对应 config/autoload/server.php 内的 server 所对应的 name 值
        // 按照这里的顺序执行
        'http' => [
            \App\Exception\Handler\FooExceptionHandler::class,
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
            \Hyperf\Validation\ValidationExceptionHandler::class,

            // JWT 这个最好自定义，EX: FooException::class (能有效的获取报错信息内容)
//            \Qbhy\HyperfAuth\AuthExceptionHandler::class,
        ],
    ],
];
