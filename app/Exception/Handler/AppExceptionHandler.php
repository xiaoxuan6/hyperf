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

namespace App\Exception\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(StdoutLoggerInterface $logger, ConfigInterface $config)
    {
        $this->logger = $logger;
        $this->config = $config;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        /**
         * 处理数据不存在的报错
         */
        if ($throwable instanceof ModelNotFoundException) {
            return response()->json(["code" => 500, "msg" => $throwable->getMessage()]);
        }

        $env = $this->config->get("app_env");
        if ($env == "dev" || $env == "local") {
            return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(new SwooleStream($throwable->getFile() . " : " . $throwable->getLine() . " line => " . $throwable->getMessage()));
        } else {
            return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
        }
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
