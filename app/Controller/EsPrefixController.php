<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Oauth;
use App\Services\EsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController(prefix="/prefix")
 */
class EsPrefixController
{
    /**
     * @Inject()
     * @var EsService
     */
    protected $client;

    protected $index = "es_hyperf_prefix";

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!' . __METHOD__);
    }

    public function createIndex()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "settings" => [
                    "number_of_shards"   => 1,
                    "number_of_replicas" => 0
                ],
                "mappings" => [
                    "dynamic"    => false,
                    "properties" => [
                        "name"        => [
                            "type"           => "text",
                            "index_prefixes" => [
                                "min_chars" => 1, // 要索引的最小前缀长度。必须大于0，并且默认为2。该值包含在内。
                                "max_chars" => 3 // 要索引的最大前缀长度。必须小于20，默认为5。该值包含在内。
                            ]
//                            "analyzer" => "ik_max_word"
                        ],
                        "age"         => [
                            "type" => "integer"
                        ],
                        "descirption" => [
                            "type"       => "nested",
                            "properties" => [
                                "price" => [
                                    "type" => "integer"
                                ],
                                "sex"   => [
                                    "type" => "keyword"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return success($this->client->indices()->create($params));
    }

    public function create()
    {
        $index = $this->index;

        Oauth::query()
            ->chunkById(10, function ($oauths) use ($index) {

                $params = ["body" => []];
                foreach ($oauths as $item) {
                    $params["body"][] = [
                        "index" => [
                            "_index" => $index,
                            "_id"    => $item->id
                        ]
                    ];

                    $params["body"][] = $item->toArray();
                }

                $this->client->bulk($params);
            });

        return success("ok");
    }

    public function search()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "query" => [
//                    "prefix" => [
//                        "name" => [
//                            "value" => "eto123_"
//                        ]
//                    ]
                    "prefix" => [
                        "name" => "1618466614_"
                    ]
                ]
            ]
        ];

        return success($this->client->search($params));
    }

    /*********************************************************************************************
     * 1、加快前缀查询；可以使用 index_prefixes 参数，加快查询速度；如果使用，Elasticsearch 会在一个单独的字段中索引2到5个字符之间的前缀。
     *  这使得Elasticsearch 可以以更大的索引为代价，更有效的运行前缀索引。
     *
     * 2、允许昂贵查询，如果search.allow_expensive_queries 设置为false，则不会执行前缀查询。但是，如果index_prefixes启用，则会构建一个优化的查询，
     * 该查询并不算慢，尽管有此设置也将执行该查询。
     **********************************************************************************************/
}
