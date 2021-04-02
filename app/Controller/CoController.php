<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Parallel;
use Hyperf\Utils\WaitGroup;
use Swoole\Coroutine\Channel;

/**
 * @AutoController()
 */
class CoController
{
    /**
     * @Inject()
     * @var ClientFactory
     */
    private $clientFatory;

    public function index()
    {
        return 123;
    }

    /**
     * Notes: 创建协成
     */

    public function index1()
    {
        Coroutine::create(function () {
            sleep(1);
            var_dump(1);
        });
    }

    public function index2()
    {
//        co(function(){
        // or
        go(function () {
            sleep(2);
            var_dump(2);
        });
    }

    /**
     * Notes: 判断当前是否处于协程环境中
     * Date: 2021/3/29 13:57
     * @return bool
     */
    public function isCoroutine()
    {
        return Coroutine::inCoroutine();
    }

    public function coId()
    {
        return Coroutine::id();
    }

    /**
     * Notes: 测试 demo
     */

    public function sleep(RequestInterface $request)
    {
        $seconds = $request->input("seconds", 2);
        sleep($seconds);
        return $seconds;
    }

    public function test()
    {
        $wg = new WaitGroup();

        $result = [];
        for ($i = 0; $i < 5; $i++) {
            $wg->add(1);
            co(function () use ($wg, &$result, $i) {
                $result[$i] = $i;
                $wg->done();
            });
        }

        $wg->wait();

        return $result;
    }

    public function demo3()
    {
        $result = \parallel([
            "foo" => function () {
                $client = $this->clientFatory->create();
                $response = $client->get("http://127.0.0.1:2698/co/sleep?secods=2");

                return $response->getBody()->getContents();
            },
            "bar" => function () {
                $client = $this->clientFatory->create();
                $client->get("http://127.0.0.1:2698/co/sleep?secods=2");
                return 321;
            }
        ]);

        return $result;
    }

    public function demo2()
    {
        $parallel = new Parallel();
        $parallel->add(function () {
            $client = $this->clientFatory->create();
            $client->get("http://127.0.0.1:2698/co/sleep?secods=2");
            return 123;
        }, "foo");
        $parallel->add(function () {
            $client = $this->clientFatory->create();
            $client->get("http://127.0.0.1:2698/co/sleep?secods=2");
            return 321;
        }, "bar");

        $result = $parallel->wait();
        return $result;
    }

    public function demo1()
    {
        $wg = new WaitGroup();
        $wg->add(2);

        $result = [];
        $coCoroutine = [];
        co(function () use ($wg, &$result, &$coCoroutine) {
            $client = $this->clientFatory->create();
            $client->get("http://127.0.0.1:2698/co/sleep?secods=2");
            $result[] = 123;

            // 获取当前协程 ID
            $coCoroutine["first"] = Coroutine::id();

            // 使用 defer (先进后出)
            defer(function () use (&$coCoroutine) {
                $coCoroutine["first"] = __LINE__ . ":" . $coCoroutine["first"]; // 这里不能使用 __LINE__， 因为协程会在 runtime 文件下生产缓存文件和当前文件不一样
            });

            $wg->done();
        });
        co(function () use ($wg, &$result, &$coCoroutine) {
            $client = $this->clientFatory->create();
            $client->get("http://127.0.0.1:2698/co/sleep?secods=2");
            $result[] = 321;

            // 获取当前协程 ID
            $coCoroutine["second"] = Coroutine::id();
            $wg->done();
        });

        $wg->wait();
        return $result + $coCoroutine;
    }

    public function demo()
    {
        $channle = new Channel();
        co(function () use ($channle) {
            $client = $this->clientFatory->create();
            $client->get("127.0.0.1:2698/co/sleep");
            $channle->push(123);
        });
        co(function () use ($channle) {
            $client = $this->clientFatory->create();
            $client->get("127.0.0.1:2698/co/sleep");
            $channle->push(321);
        });

        $result[] = $channle->pop();
        $result[] = $channle->pop();

        return $result;
    }
}
