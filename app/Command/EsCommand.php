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

        } else {

            $this->info("索引 {$this->index} 已存在，准备删除重新创建");
            $this->deleteIndices();
            $this->info("删除成功，准备创建");
            $this->reindex();
            $this->info("创建成功，准备添加数据");

        }

        try {
            $this->bulk();
            $this->info("数据添加成功");
        } catch (ClientErrorResponseException $exception) {
            $this->error("数据添加失败");
        }

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

    public function reindex()
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
                        "name"        => ["type" => "text"],
                        "age"         => ["type" => "integer"],
                        "password"    => ["type" => "integer"],
                        "descirption" => [
                            "type"       => "nested",
                            "properties" => [
                                "price" => [
                                    "type"    => "integer",
                                    "copy_to" => "de_price",
                                ],
                                "sex"   => [
                                    "type"            => "text",
                                    "analyzer"        => "ik_max_word", // 字段文本的分词器
                                    "search_analyzer" => "ik_max_word", // 搜索词的分词器
                                    "copy_to"         => "de_sex",

                                    // ik_max_word分词器是插件ik提供的，可以对文本进行最大数量的分词。

                                    /**
                                     * 使用聚會时需要添加该字段(请注意，这可能会占用大量内存)
                                     *      Fielddata针对text字段在默认时是禁用的
                                     * @see https://www.cnblogs.com/sanduzxcvbnm/p/12092298.html
                                     */
                                    // "fielddata" => true
                                ]
                            ]
                        ],
                        "created_at"  => [
                            "type"   => "date",
                            "format" => "yyyy-MM-dd HH:mm:ss"
                        ]
                    ]
                ],
                "aliases"  => [$this->index => new \stdClass()]
            ]
        ];

        $this->client->indices()->create($params);
    }

    public function deleteIndices()
    {
        $result = $this->client->indices()->getAliases(["index" => $this->index]);

        if ($result && is_array($result)) {
            $indexName = current(array_keys($result));
        }

        $this->client->indices()->delete(["index" => $indexName]);
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
