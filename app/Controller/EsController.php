<?php

declare(strict_types=1);

namespace App\Controller;

use Elasticsearch\Client;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController()
 */
class EsController
{
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
        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $client->index([
            "index" => "hyperf_demo",
            "id"    => 1000,
            "body"  => [
                "query" => "key"
            ]
        ]);

        return success($result);
    }

    public function get()
    {
        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $client->get([
            "index" => "hyperf_demo",
            "type"  => "_doc",
            "id"    => 1000,
        ]);

        return success($result);
    }

    public function search()
    {
        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $client->search([
            "index" => "hyperf_demo",
            "type"  => "_doc",
            "body"  => [
                "query" => [
                    "match" => [
                        "query" => "key"
                    ]
                ]
            ]
        ]);

        return success($result);
    }

    public function delete()
    {
        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $client->delete([
            "index" => "hyperf_demo",
            "type"  => "_doc",
            "id"    => 1000,
        ]);

        return success($result);
    }


    /**
     * Notes: 索引是否存在
     * Date: 2021/4/12 15:57
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function exists()
    {
        $client = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();

        $result = $client->indices()->exists(["index" => "hyperf"]);

        return success($result);
    }
}
