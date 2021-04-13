<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Oauth;
use App\Services\EsService;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class EsCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $index = "es_hyperf_demos";

    /**
     * @Inject()
     * @var EsService
     */
    protected $client;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('es:init');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    /**
     * Notes:
     * Date: 2021/4/12 17:40
     * @return string
     */
    public function handle()
    {
        if (!$result = $this->client->indices()->exists(["index" => $this->index])) {

            $this->info("索引 {$this->index} 不存在，准备创建");
            $this->createIndices();
            $this->info("索引创建成功，准备添加数据");

            try {
                $this->bulk();
                $this->info("数据添加成功");
            } catch (ClientErrorResponseException $exception) {
                $this->error("数据添加失败");
            }
            return "successfull";
        }

        $this->info("索引 {$this->index} 已存在");
        return "ok";
    }

    public function createIndices()
    {
        $params = [
            "index" => $this->index . "_0",
            "body"  => [
                "settings" => [
                    "number_of_shards"   => 1,
                    "number_of_replicas" => 0,
                ],
                "mappings" => [
                    "dynamic"    => false,
                    "properties" => [
                        "name"     => ["type" => "text"],
                        "age"      => ["type" => "integer"],
                        "password" => ["type" => "integer"]
                    ]
                ],
                "aliases"  => [$this->index => new \stdClass()]
            ]
        ];

        $this->client->indices()->create($params);
    }

    public function bulk()
    {
        $index = $this->index;

        Oauth::query()
            ->chunkById(10, function ($oauths) use ($index) {

                $params = ["body" => []];
                foreach ($oauths as $oauth) {

                    $params['body'][] = [
                        "index" => [
                            "_index" => $index,
                            "_id"    => $oauth->id,
                        ]
                    ];

                    $params["body"][] = $oauth->toArray();
                }

                $this->client->bulk($params);

            });
    }
}
