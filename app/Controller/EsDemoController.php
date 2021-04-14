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

    public function create()
    {
        $data = [
            "price" => 100,
            "sex"   => rand(0, 1) ? "M" : "N"
        ];

        Oauth::query()->create(["name" => time(), "age" => rand(10, 100), "password" => str_pad((string)rand(0, 9999), 4, "0"), "descirption" => $data]);

        return success("添加成功");
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
                                        "descirption.sex" => "N"
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
}
