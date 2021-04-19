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
                        "filter"      => [
                            "stop_filter"    => [
                                "type"      => "stop",
                                "stopwords" => [
                                    "停用词" // 这里会根据中文分词拆分成：停、用、词
                                ],
                                "ignore_case" => "true", // 停用词匹配不区分大小写
                                // "stopwords_path" => "", // 包含要删除的停用词列表的文件路径
                            ],
                            "synonym_filter" => [
                                "type"     => "synonym",
                                "synonyms" => [
                                    "测试,test"
                                ]
                                // "synonyms_path" => ""
                            ],
                            "limit_filter"   => [
                                "type"            => "limit",
                                "max_token_count" => 10
                            ]
                        ],
                        "analyzer"    => [
                            "new_analyzer" => [
                                "type"        => "custom", // 分析仪类型
                                "tokenizer"   => "ik_max_word", // 中文分词器 （分词器在字符过滤器之后工作，用于把文本分割成多个标记（Token），一个标记基本上是词加上一些额外信息，分词器的处理结果是标记流，它是一个接一个的标记，准备被过滤器处理。ElasticSearch 2.4版本内置很多分词器，本节简单介绍常用的分词器。）
                                "char_filter" => ["html_strip", "en_to_zh"], // 字符过滤器（字符过滤器对未经分析的文本起作用，作用于被分析的文本字段（该字段的index属性为analyzed），字符过滤器在分词器之前工作，用于从文档的原始文本去除HTML标记（markup），或者把字符“&”转换为单词“and”。ElasticSearch 2.4版本内置3个字符过滤器，分别是：映射字符过滤器（Mapping Char Filter）、HTML标记字符过滤器（HTML Strip Char Filter）和模式替换字符过滤器（Pattern Replace Char Filter）。）
                                "filter"      => [
                                    "lowercase",
                                    "stop_filter",
                                    "synonym_filter",
                                    "limit_filter"
                                ] // 令牌过滤器 （分析器包含零个或多个标记过滤器，标记过滤器在分词器之后工作，用来处理标记流中的标记。标记过滤从分词器中接收标记流，能够删除标记，转换标记，或添加标记。ElasticSearch 2.4版本内置很多标记过滤器，本节简单介绍常用的过滤器。
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
        /*$params = [
            "index" => $this->index,
            "id"    => 1,
            "body"  => [
                "name"     => "自定义analyzer，这是 first one",
//                "name"     => "自定义analyzer，这是 first two",
                "age"      => 18,
                "password" => 123456,
                "class"    => "no:100"
            ]
        ];*/

        $params = [
            "index" => $this->index,
            "id"    => 10,
            "body"  => [
                "name"     => "this is an es and the first three, 这是测试停用词，这是测试分词个数限制",
                "age"      => 25,
                "password" => 123123,
                "class"    => 'no:three'
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
//                                "name" => "2"
                                // 停用词
//                                "name" => "用"
                                // 同义词
//                                "name" => "test"
                                "name" => "长度"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return success($this->client->search($params));
    }
}
