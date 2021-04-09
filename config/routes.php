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

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

//Router::addRoute(["GET"], "/redis", "App\Controller\IndexController@cache");
Router::get("/cache", "App\Controller\IndexController@cc");

// 路由组：注意 "/"
Router::addGroup("/group/", function () {
    Router::get("index", function () { return "index"; });
    Router::get("list", function () { return "list"; });
});

Router::addGroup("/oauth/", function () {
    Router::get("index", "App\Controller\DemoController@index");
    Router::post("create", "App\Controller\DemoController@store");
    Router::post("update", "App\Controller\DemoController@update");
    Router::get("delete", "App\Controller\DemoController@delete");
}, ["middleware" => [\App\Middleware\CoreMiddleware::class]]);

//Router::get("/config/get", "App\Controller\ConfigController@index");
//Router::get("/config/update", "App\Controller\ConfigController@update");