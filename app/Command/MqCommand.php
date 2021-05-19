<?php

declare(strict_types=1);

namespace App\Command;

use App\Amqp\Consumer\DemoConsumer;
use Hyperf\Amqp\Consumer;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class MqCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('demo:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setName("mq:consumer");
        $this->setDescription('消费 MQ 队列数据');
    }

    public function handle()
    {
        $this->output->writeln(sprintf('<fg=green>%s</fg>', "消费者开始监听..."));

        $producer = ApplicationContext::getContainer()->get(Consumer::class);
        $resulr = $producer->consume(new DemoConsumer());

        return success("消费队列消息：" . $resulr);
    }
}
