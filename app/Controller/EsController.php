<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\EsService;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController()
 */
class EsController
{
    /**
     * @Inject()
     * @var EsService
     */
    public $client;

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    public function client()
    {
        /*** @var $client Client */
        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $info = $client->info();

        return success($info);
    }

    public function create()
    {
//        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $this->client->index([
            "index" => "hyperf_demo",
            "id"    => 1000,
            "body"  => [
                "query" => "key"
            ]
        ]);

        return success($result);
    }

    public function update()
    {
        $result = $this->client->update([
            "index" => "hyperf_demo",
            "id"    => 1000,
            "body"  => [
                "doc" => [
                    "query"    => "key more operation",
                    "keywords" => "elasticsearch hyperf"
                ]
            ]
        ]);

        return success($result);
    }

    public function get()
    {
//        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $this->client->get([
            "index" => "hyperf_demo",
            "type"  => "_doc",
            "id"    => 1000,
        ]);

        return success($result);
    }

    public function search()
    {
//        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $this->client->search([
            "index" => "hyperf_demo",
            "type"  => "_doc",
            "body"  => [
                "query" => [
                    "term" => [
                        "query" => "key"
                    ]
                ]
            ]
        ]);

        return success($result);
    }

    public function delete()
    {
//        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $this->client->delete([
            "index" => "hyperf_demo",
            "type"  => "_doc",
            "id"    => 1000,
        ]);

        return success($result);
    }

    public function getSource()
    {
        $params = [
            "index" => "hyperf_demo",
            "id"    => 1000,
        ];

        $result = $this->client->getSource($params);

        return success($result);
    }


    /**
     * Notes: 索引是否存在
     * Date: 2021/4/12 15:57
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function exists()
    {
//        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $this->client->indices()->exists(["index" => "hyperf"]);

        return success($result);
    }

    public function createIndices()
    {
        $params = [
            "index" => "demo_indices_1",
            "body"  => [
                'settings' => [
                    'number_of_shards'   => 5,
                    'number_of_replicas' => 1
                ]
            ]
        ];

        try {

            $result = $this->client->indices()->create($params);

            return success($result);
        } catch (ClientErrorResponseException $exception) {
            return fail($exception->getMessage());
        }
    }

    public function getIndices()
    {
        $result = $this->client->indices()->get(["index" => "demo_indices_1"]);

        return success($result);
    }

    public function delIndices(RequestInterface $request)
    {
        $result = $this->client->indices()->delete(["index" => $request->input("index")]);

        return success($result);
    }

    public function getIndicesAlias(RequestInterface $request)
    {
        $result = $this->client->indices()->getAliases(["index" => $request->input("index")]);

        // 获取第一个索引名
        if($result && is_array($result)) {
            $indexName = current(array_keys($result));
        }

        return success($indexName);
    }

    public function updateIndicesAlias()
    {
        $result = $this->client->indices()->updateAliases([
            "body" => [
                "actions" => [
                    "add" => [
                        "index" => "hyperf_demo",
                        "alias" => "hyperf_demo_01",
                    ]
                ]
            ]
        ]);

        return success($result);
    }
}
