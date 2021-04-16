<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\EsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController(prefix="/route")
 */
class EsRouteController
{
    /**
     * @Inject()
     * @var EsService
     */
    protected $client;

    protected $index = "es_hyperf_route";

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    public function createIndex()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "settings" => [
                    "number_of_shards"   => 2, // 这里创建两个分片，用于添加文档是根据路由保存到那个分片上
                    "number_of_replicas" => 0,
                ],
                "mappings" => [
                    "dynamic"    => false,
                    "_routing"   => [
                        "required" => true, // 必须验证路由(创建文档的时候必须添加 routing 路由)
                    ],
                    "properties" => [
                        "name"     => ["type" => "text"],
                        "age"      => ["type" => "integer"],
                        "password" => ["type" => "integer"],
                        "class"    => ["type" => "keyword"],
                    ]
                ]
            ]
        ];

        $this->client->indices()->create($params);

        return success("ok");
    }

    public function create()
    {
        $target = rand(0, 1);
        $bodys = [
            [
                "name"     => "route",
                "age"      => 100,
                "password" => 88888,
                "class"    => "no:1",
            ],
            [
                "name"     => "test_route",
                "age"      => 10,
                "password" => 66666,
                "class"    => "no:1",
            ]
        ];

        $params = [
            "index"   => $this->index,
            "routing" => $target == 0 ? "A" : "B", // 上面创建的索引必须使用routing
            "id"      => rand(10, 100), // 这是文档的id，如果没有创建新文档，否则修改（相同的id可以存储在不同的分片上）
            "body"    => $bodys[$target]
        ];

        $result = $this->client->index($params);

        return success($result);
    }

    public function search()
    {
        // 随机查询某个分片上的所有文档
        /*$params = [
            "index"   => $this->index,
            "routing" => rand(0, 1) == 0 ? "A" : "B",
            "body"    => [
                "query" => [
                    "match_all" => new \stdClass(),
                ]
            ]
        ];*/

        // 和上面效果相同（区别在于 routing 使用）
//        $params = [
//            "index" => $this->index,
//            "body"  => [
//                "query" => [
//                    "terms" => [
//                        "_routing" => ["A"]
//                    ]
//                ]
//            ]
//        ];

        // 查询分片 A 上面的 password 小于 100000 的所有文档
        /*$params = [
            "index"   => $this->index,
            "routing" => "A",
            "body"    => [
                "query" => [
                    "bool" => [
                        "must"   => [
                            "match_all" => new \stdClass()
                        ],
                        "filter" => [
                            "range" => [
                                "password" => [
                                    "lte" => 100000
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];*/

        // 查询多个分片
        $params = [
            "index"   => $this->index,
            "routing" => ["A", "B"],
            "body"    => [
                "query" => [
                    "bool" => [
                        "filter" => [
                            "term" => [
                                "age" => 100
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);

        return success($result);
    }

    public function getIndex()
    {
        $result = $this->client->get([
            "index"   => $this->index,
            "routing" => "A",
            "id"      => 1
        ]);

        return success($result);
    }

    /*******************************************************************************************************************************************
     * 总结：使用路由之后，不管是对文档的 curd 都需要添加路由 routing，否则报错："reason":"routing is required for [es_hyperf_route]/[_doc]/[1]"
     *
     * routing：这里指的是自定义的名字
     *
     * 使用以下公式将文档路由到索引中的特定分片：
     *      shard_num = hash(_routing) % num_primary_shards
     *
     * 文档数量：
     *      在一个 shard 中：文档数量不能超过 2^31，即 2147483648 条。
     *
     * 内存大小：
     *      建议不要超过 50GB。
     *      原因： 1. 太大会影响数据进行再平衡（例如发生故障后）时移动分片的速度。 2. 太大会影响查询速度。
     ******************************************************************************************************************************************/
}
