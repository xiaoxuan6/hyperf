<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\OauthEvent;
use App\Utils\Facade\Log;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener
 */
class OauthListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            OauthEvent::class
        ];
    }

    /*
     * @Inject()
     * @var LoggerFactory
     */
//    private $loggerFacotry;

    /**
     * @param OauthEvent $event
     */
    public function process(object $event)
    {
//        $log = $this->loggerFacotry->get("logger");
//        $log->info("这是 Oauth 事件", $event->oauth->toArray());

        Log::info(__METHOD__, $event->oauth->toArray());
    }
}
