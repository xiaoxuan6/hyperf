<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\EsService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController(prefix="analyzer")
 */
class EsAnalyzerController
{
    /**
     * @Inject()
     * @var EsService
     */
    protected $client;

    protected $index = "es_hyperf_analyzer";

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
                    "analysis"           => [
                        // https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-charfilters.html
                        "char_filter" => [
                            // HTML条带字符过滤器 (该html_strip字符过滤带出HTML元素，比如<b>像和解码HTML实体&amp;。)
                            // "html_strip", // 这个一般直接放在下面
                            // 映射字符过滤器 (该mapping字符过滤器替换指定更换指定的字符串中的任何事件。)
                            "en_to_zh" => [
                                "type"     => "mapping",
                                "mappings" => ["one => 1", "two => 2", "three => 3"]
                            ]
                            // 模式替换字符过滤器 (pattern_replace字符过滤器将所有与正则表达式匹配 的字符替换为指定的替换。)
                        ],
                        "analyzer"    => [
                            "new_analyzer" => [
                                "type"        => "custom", // 分析仪类型
                                "tokenizer"   => "ik_max_word", // 中文分词器
                                "char_filter" => ["html_strip", "en_to_zh"], // 字符过滤器
                                "filter"      => ["lowercase"] // 令牌过滤器
                            ]
                        ]
                    ]
                ],
                "mappings" => [
                    "dynamic"    => false,
                    "properties" => [
                        "name"     => [
                            "type"     => "text",
                            "analyzer" => "new_analyzer"
                        ],
                        "age"      => ["type" => "integer"],
                        "password" => ["type" => "integer"],
                        "class"    => ["type" => "keyword"],
                    ]
                ]
            ]
        ];

        return success($this->client->indices()->create($params));
    }

    public function create()
    {
        $params = [
            "index" => $this->index,
            "id"    => 1,
            "body"  => [
                "name"     => "自定义analyzer，这是 first one",
//                "name"     => "自定义analyzer，这是 first two",
                "age"      => 18,
                "password" => 123456,
                "class"    => "no:100"
            ]
        ];

        $result = $this->client->index($params);

        return success($result);
    }

    public function search()
    {
        $params = [
            "index" => $this->index,
            "body"  => [
                "query" => [
                    "bool" => [
                        "must" => [
                            "match" => [
//                                "name" => "这是"
//                                "name" => "自定义"
                                "name" => "2"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return success($this->client->search($params));
    }
}
