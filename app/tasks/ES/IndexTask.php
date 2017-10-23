<?php

namespace App\Tasks\ES;

use App\Support\Elasticsearch\Client;
use App\Support\Elasticsearch\ES;
use App\Tasks\Task;
use Xin\Cli\Color;

class IndexTask extends Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  ElasticSearch测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run es:index@[action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  info              搜索引擎信息', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  create            创建index', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  mapping           创建mapping', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  getMapping        读取mapping', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  del               删除整个索引', Color::FG_GREEN) . PHP_EOL;

    }

    public function delAction()
    {
        $client = Client::getInstance();
        $indices = $client->indices();

        $params = [
            'index' => ES::ES_INDEX,
        ];
        try {
            $res = $indices->delete($params);
            if ($res['acknowledged']) {
                echo Color::success('删除索引成功') . PHP_EOL;
            }
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            if ($res) {
                echo Color::colorize($res['error']['reason'], Color::FG_LIGHT_RED) . PHP_EOL;
            } else {
                echo Color::colorize($ex->getMessage(), Color::FG_LIGHT_RED) . PHP_EOL;
            }
        }
    }

    public function getMappingAction()
    {
        $client = Client::getInstance();
        $indices = $client->indices();

        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
        ];
        try {
            $res = $indices->getMapping($params);
            dd($res);
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            if ($res) {
                echo Color::colorize($res['error']['reason'], Color::FG_LIGHT_RED) . PHP_EOL;
            } else {
                echo Color::colorize($ex->getMessage(), Color::FG_LIGHT_RED) . PHP_EOL;
            }
        }
    }

    public function mappingAction()
    {
        $client = Client::getInstance();
        $indices = $client->indices();

        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'body' => [
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'short'],
                    'birthday' => ['type' => 'date'],
                    'location' => ['type' => 'geo_point'],
                    'randnum' => ['type' => 'long'],
                ],
            ],
        ];
        try {
            $res = $indices->putMapping($params);
            if ($res['acknowledged']) {
                echo Color::success('Mapping 设置成功') . PHP_EOL;
            }
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            if ($res) {
                echo Color::colorize($res['error']['reason'], Color::FG_LIGHT_RED) . PHP_EOL;
            } else {
                echo Color::colorize($ex->getMessage(), Color::FG_LIGHT_RED) . PHP_EOL;
            }
        }
    }

    public function infoAction()
    {
        $client = Client::getInstance();
        dd($client->info());
    }

    public function createAction()
    {
        $client = Client::getInstance();
        $indices = $client->indices();

        $params = [
            'index' => ES::ES_INDEX,
        ];
        try {
            $res = $indices->create($params);
            if ($res['acknowledged']) {
                echo Color::success('Index 创建成功') . PHP_EOL;
            }
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            // dump($res);
            echo Color::colorize($res['error']['reason'], Color::FG_LIGHT_RED) . PHP_EOL;
        }
    }

}

