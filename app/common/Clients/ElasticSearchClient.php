<?php
// +----------------------------------------------------------------------
// | EsClient.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Common\Clients;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticSearchClient
{
    public static $_instance;

    /** Client */
    public static function getInstance()
    {
        if (isset(static::$_instance) && static::$_instance instanceof Client) {
            return static::$_instance;
        }
        $config = di('config')->elastic;
        $host = $config->get('host', '127.0.0.1:9200');
        return static::$_instance = ClientBuilder::create()->setHosts([$host])->build();
    }
}