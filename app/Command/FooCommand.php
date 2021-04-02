<?php

declare(strict_types=1);

namespace App\Command;

use App\Utils\Facade\Log;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @Command
 */
class FooCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var LoggerFactory
     */
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('foo:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
        $this->addArgument("name", InputArgument::OPTIONAL, '姓名', "eto");
        $this->addOption("age", "a", InputOption::VALUE_REQUIRED, "年龄", 18);
    }

    public function handle()
    {
        $name = $this->input->getArgument("name");
        $age = $this->input->getOption("age");

//        $this->line(sprintf('Hello %s，今年 %s 岁了', $name, $age), 'info');

        $logger = $this->logger->get("logger");
        $logger->info(sprintf('Hello %s，今年 %s 岁了', $name, $age));

        // 使用自己封装的日志类
        Log::info(sprintf('Hello %s，今年 %s 岁了', $name, $age));
    }

    /**
     * Notes: 进度条
     * Date: 2021/3/29 18:18
     */
    public function progress()
    {
        $this->output->progressStart(100);

        for ($i = 0; $i < 10; $i++) {
            $this->output->progressAdvance(10);
            sleep(1);
        }

        $this->output->progressFinish();
    }
}
