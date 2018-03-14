<?php
// +----------------------------------------------------------------------
// | Client.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Support\Elasticsearch;

use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\ClientBuilder;

class Client
{
    public static $_instance;

    public static function getInstance()
    {
        if (isset(static::$_instance) && static::$_instance instanceof ElasticsearchClient) {
            return static::$_instance;
        }
        $host = env('ELASTIC_SEARCH_HOST');
        return static::$_instance = ClientBuilder::create()->setHosts([$host])->build();
    }
}
