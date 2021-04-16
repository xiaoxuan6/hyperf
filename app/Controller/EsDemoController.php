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
 * @AutoController()
 */
class EsDemoController
{
    /**
     * @Inject()
     * @var EsService
     */
    protected $client;

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    public function create(RequestInterface $request)
    {
        $data = [
            "price" => rand(10, 100),
//            "sex"   => rand(0, 1) ? "M" : "N"
            "sex"   => rand(0, 1) ? "性别：男" : "性别：女"
        ];

        $oauth = Oauth::query()->create([
            "name"        => $request->input("name", time()),
            "age"         => rand(10, 100),
            "password"    => str_pad((string)rand(0, 9999), 4, "0"),
            "descirption" => $data,
            "class"       => $request->input("class", "vinhson")
        ]);

        $params = [
            "index" => "es_hyperf_demos",
            "id"    => $oauth->id,
            "body"  => $oauth->toArray()
        ];

        $this->client->index($params);

        return success("添加成功");
    }

    /**
     * Notes: 使用聚合对文档进行分组统计
     * Date: 2021/4/15 19:32
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function searchFields()
    {
        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "query"   => [
                    "bool" => [
                        "must" => [
                            "match" => [
                                "name" => "eto"
                            ]
                        ]
                    ]
                ],
                "sort"    => [
                    "name.raw" => "desc"
                ],
                "_source" => [
                    "id",
                    "name",
                    "age",
                ],
                "aggs"    => [
                    "name_count" => [
                        "terms" => [
                            "field" => "name.raw"
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);

        return success($result);
    }

    /**
     * Notes: 修改索引映射字段（允许你向现有索引添加字段，或者仅更改现有字段的搜索设置。）
     * Date: 2021/4/15 19:07
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putMapping()
    {
        $result = $this->client->indices()->getAliases(["index" => "es_hyperf_demos"]);

        if ($result && is_array($result)) {
            $index = current(array_keys($result));
        }

        $this->client->indices()->close(["index" => $index]);

        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "dynamic"    => false,
                "properties" => [
                    "name" => [
                        "type"   => "text",
                        "fields" => [
                            "raw" => [
                                "type" => "keyword"
                            ]
                        ]
                    ],
                ]
            ]
        ];

        $result = $this->client->indices()->putMapping($params);

        $this->client->indices()->open(["index" => $index]);

        return success($result);
    }

    /**
     * Notes: 测试 ignore_above 是否有效（mapping 字段类型只针对 keyword 有效）
     * Date: 2021/4/15 15:30
     * @param RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    /**
     * keyword 和 text 的区别：
     *      keyword 不支持全文搜索。所以，只能是使用精确匹配进行查询，比如 term 查询。
     *      text 默认支持全文搜索。比如 match 查询
     *      text类型,用于全文索引. 使用 keyword 类型用于排序(sort) 或者聚合(aggregations)
     */
    public function searchParams(RequestInterface $request)
    {
        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "query" => [
                    "term" => [
                        "class" => $request->input("class", "vinhson")
                    ],
                    // 添加 class = 你好啊， 使用如下是搜不出来结果的，keyword不支持分词查找
                    /*"bool" => [
                        "must" => [
                            "match" => [
                                "class" => "你好"
                            ]
                        ]
                    ]*/
                ]
            ]
        ];

        $result = $this->client->search($params);

        return success($result);
    }

    public function search()
    {
        $result = $this->searchNestedSex();
        return success($result);
    }

    /**
     * Notes: 搜索 created_at 大于某个时间的数据
     * Date: 2021/4/14 10:47
     * @return mixed
     */
    protected function searchData()
    {
        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "query" => [
                    "bool" => [
                        "must"   => [
                            "match_all" => new \stdClass(),
                        ],
                        "filter" => [ // 这里有个暗坑（filter 单独使用需要在外层包一层“filtered”,但是某个版本之后弃用筛选的查询）
                            "range" => [
                                "created_at" => [
                                    "gte" => "2021-03-31 17:10:59"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $result = $this->client->search($params);
    }

    /**
     * query filter在性能上对比：filter是不计算相关性的，同时可以cache。因此，filter速度要快于query。
     */

    protected function searchNestedSex()
    {
        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "query" => [
                    "nested" => [
                        "path"  => "descirption",
                        "query" => [
                            "bool" => [
                                "must" => [
                                    "match" => [
//                                        "descirption.sex" => "N"
                                        "descirption.sex" => "女"
                                        // 使用 copy_to 字段不需要使用 nested 搜索
//                                        "de_sex" => 'M'
//                                        "de_id" => 100
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->client->search($params);
    }

    /**
     * Notes: 统计所有的子文档并统计总数
     * Date: 2021/4/14 19:09
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getNestedAllByCount()
    {
        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "query" => [
                    "nested" => [
                        "path"  => "descirption",
                        "query" => [
                            "match_all" => new \stdClass()
                        ]
                    ]
                ],
                "aggs"  => [
                    "nested_count" => [
                        "nested" => [
                            "path" => "descirption"
                        ],
                        "aggs"   => [
                            "count" => [
                                "value_count" => [
                                    "field" => "descirption.price"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);

        return success($result);
    }

    /**
     * Notes: 只统计子文档的个数
     * Date: 2021/4/14 19:30
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getNestedCount()
    {
        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "size" => 0, //等于0表示只获取聚合结果，而不需要执行觉和的原始数据；
                "aggs" => [ // 固定语法，对一份数据执行分组聚合操作；
                    "nested_count" => [ // 聚合的名字，是自己取的；
                        "nested" => [
                            "path" => "descirption"
                        ],
                        "aggs"   => [
                            "count" => [
                                "value_count" => [
                                    "field" => "descirption.price" // 根据指定的字段进行统计；
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);

        return success($result);
    }

    /**
     * Notes: 搜索所有的子文档并且按照子文档中的price排序
     * Date: 2021/4/15 9:59
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getNestedAll()
    {
        $params = [
            "index" => "es_hyperf_demos",
            "body"  => [
                "query" => [
                    "nested" => [
                        "path"  => "descirption",
                        "query" => [
                            "match_all" => new \stdClass()
                        ]
                    ]
                ],
                "sort"  => [
                    "descirption.price" => [
                        "order"         => "asc",
                        "nested_path"   => "descirption",
                        "nested_filter" => [
                            "bool" => [
                                "must" => [
                                    "match_all" => new \stdClass()
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->client->search($params);

        return success($result);
    }

    /**
     * 总结：
     *      1、嵌套文档进行增加、修改或者删除时，整个文档都要重新被索引。嵌套文档越多，这带来的成本就越大。
     *      2、对嵌套文档排序或聚合操作的时候，必须在该操作里面添加搜索的条件，避免查询出来的数据和排序或聚合数据不一致
     */
}
