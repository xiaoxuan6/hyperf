<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Producer\DemoProducer;
use Hyperf\Amqp\Producer;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\ApplicationContext;

/**
 * Class AMQPController
 * @AutoController(prefix="/mq")
 */
class AMQPController
{
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    public function send()
    {
        $data = __METHOD__ . " 你好";

        $proucer = ApplicationContext::getContainer()->get(Producer::class);
        $proucer->produce(new DemoProducer($data));

        return success($data);
    }
}
