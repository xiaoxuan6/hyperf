<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Discuss;
use App\Model\Oauth;
use App\Services\EsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController(prefix="/relation")
 */
class EsRelationController
{
    /**
     * @Inject()
     * @var EsService
     */
    protected $client;

    protected $index = "es_hyperf_relation";

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
                    "number_of_replicas" => 0,
                ],
                "mappings" => [
                    "dynamic"    => false,
                    "properties" => [
                        // 父文档
                        "name"    => [
                            "type"     => "text",
                            "analyzer" => "ik_max_word"
                        ],
                        // 重点
                        "level"   => [
                            "type"      => "join",
                            "relations" => [
                                "oauth" => "discuss"
                            ]
                        ],
                        // 子文档
                        "context" => [
                            "type"     => "text",
                            "analyzer" => "ik_max_word"
                        ],
                    ]
                ]
            ]
        ];

        return success($this->client->indices()->create($params));
    }

    /*******************************************************************************************************
     * 父-子文档的性能及限制性
     *------------------------------------------------------------------------------------------------------
     *  特性：
     * 1、每个索引只能有一个join字段
     * 2、父-子文档必须在同一个分片上，也就是说增删改查一个子文档，必须使用和父文档一样的routing key(默认是id)
     * 3、每个元素可以有多个子，但只有一个父
     * 4、可以为一个已存在的join字段添加新的关联关系
     * 5、可以在一个元素已经是父的情况下添加一个子
     ******************************************************************************************************/

    /**
     * Notes: 父文档
     * Date: 2021/4/19 14:48
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create1()
    {
        $params = [
            "index" => $this->index,
            "id"    => 2,
//            "body"  => Oauth::query()->whereKey(2)->first()->toArray() + ["level" => "oauth"] // 上面的缩写
            "body"  => Oauth::query()->whereKey(2)->first()->toArray() + ["level" => ["name" => "oauth"]]
        ];

        return success($this->client->index($params));
    }

    /**
     * Notes: 子文档
     * Date: 2021/4/19 14:49
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create2()
    {
        $params = [
            "index"   => $this->index,
            "id"      => 101,
            "routing" => 2, // 父文档的id，保持子文档和父文档保存在同一个分片上
            "body"    => Discuss::query()->where("oauth_id", 2)->first()->toArray() + ["level" => ["name" => "discuss", "parent" => 2]]
        ];

        return success($this->client->index($params));
    }

    public function search()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "query" => [
                    "match_all" => new \stdClass()
                ]
            ]
        ];

        return success($this->client->search($params));
    }

    /**
     * Notes: 通过parent_id查询子文档
     * Date: 2021/4/19 16:19
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function search1()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "query" => [
                    "parent_id" => [
                        "type" => "discuss",
                        "id"   => 2
                    ]
                ]
            ]
        ];

        return success($this->client->search($params));
    }

    /**
     * Notes: 通过父文档查询子文档
     * Date: 2021/4/19 16:30
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function search2()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "query" => [
                    "has_parent" => [
                        "parent_type" => "oauth",// 指定查询的父文档
                        "query"       => [
                            "match" => [
                                "name" => "eto"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return success($this->client->search($params));
    }

    /**
     * Notes: 通过子文档查询父文档
     * Date: 2021/4/19 16:31
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function search3()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "query" => [
                    "has_child" => [
                        "type"  => "discuss",// 指定查询的子文档
                        "query" => [
                            "match" => [
                                "context" => "体育"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return success($this->client->search($params));
    }
}
