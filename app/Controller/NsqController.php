<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Nsq\Nsq;
use Hyperf\Nsq\Nsqd\Channel;
use Hyperf\Nsq\Nsqd\Topic;
use Psr\Http\Message\ResponseInterface;

/**
 * @AutoController(prefix="/nsq")
 */
class NsqController extends AbstractController
{
    public function index(): ResponseInterface
    {
        return $this->outResponse(200, 'sd');
    }

    public function topic(): ResponseInterface
    {
        $topic = app(Topic::class);
//        $res = $topic->create('hy12');

        $res = $topic->delete('hy1');

        return $this->outResponse(200, $res);
    }

    public function channel(): ResponseInterface
    {
        $channel = app(Channel::class);
        $res = $channel->create('test', 'option');

        return $this->outResponse(200, $res);
    }

    public function push(): ResponseInterface
    {
        $nsq = make(Nsq::class);
//        $nsq->publish('hyperf', 'test nsq' . time());
//        $nsq->publish('hyperf', 'test nsq' . time(), 10);
//        $nsq->publish('hyperf', ['111111111111', '2222222222222222']);
        $nsq->publish('hyperf', ['111111111111', '2222222222222222'], 3);

        return $this->outResponse(200, '消息投递成功');
    }
}
