<?php
/**
 * Created by PhpStorm.
 * User: james.xue
 * Date: 2021/4/12
 * Time: 16:07
 */

namespace App\Services;

use Elasticsearch\Client;
use Hyperf\Elasticsearch\ClientBuilderFactory;

class EsService
{
    public $connations = [];

    private function getInstance($name = "default"): Client
    {
        if (!isset($this->connations[$name])) {
            $this->connations[$name] = app(ClientBuilderFactory::class)->create()->setHosts(["http://127.0.0.1:9200"])->build();
        }

        return $this->connations[$name];
    }

    public function __call($name, $arguments)
    {
        return $this->getInstance()->{$name}(...$arguments);
    }

}