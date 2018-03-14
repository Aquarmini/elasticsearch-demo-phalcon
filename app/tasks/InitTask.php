<?php

namespace App\Tasks;

use App\Common\Clients\ElasticSearchClient;
use App\Common\Enums\SystemCode;
use Elasticsearch\Client;
use Xin\Cli\Color;

class InitTask extends Task
{
    protected $index = SystemCode::ES_INDEX;

    protected $type = SystemCode::ES_TYPE;

    /** @var Client */
    protected $client;

    public function mainAction()
    {
        echo Color::head('初始化ES') . PHP_EOL;
        $this->client = ElasticSearchClient::getInstance();

        echo Color::head('  搜索引擎信息') . PHP_EOL;
        $this->echoInfo();

        echo Color::head('  删除已经存在的索引') . PHP_EOL;
        $this->echoDeleteExistIndex();

        echo Color::head('  创建索引') . PHP_EOL;
        $this->echoCreateIndex();

        echo Color::head('  创建索引Mapping') . PHP_EOL;
        $this->echoCreateIndexMapping();

        echo Color::head('  查看Mapping') . PHP_EOL;
        $this->echoIndexMappingDetail();

        echo Color::head('  导入数据') . PHP_EOL;
        $this->echoPutIndexDocument();

        echo PHP_EOL;
        echo Color::colorize('初始化完毕', Color::FG_LIGHT_PURPLE);
    }

    protected function echoPutIndexDocument()
    {
        $docs = di('configCenter')->get('es_docs')->toArray();
        $success = 0;
        foreach ($docs as $id => $doc) {
            $param = [
                'index' => $this->index,
                'type' => $this->type,
                'id' => $id,
                'body' => $doc
            ];

            $res = $this->client->index($param);
            if ($res['created']) {
                $success++;
            }
        }
        echo Color::colorize("    测试文档导入成功，共{$success}条", Color::FG_LIGHT_GREEN) . PHP_EOL;
    }

    protected function echoIndexMappingDetail()
    {
        $indices = $this->client->indices();

        $params = [
            'index' => $this->index,
            'type' => $this->type,
        ];

        $res = $indices->getMapping($params);
        if ($mapping = $res[$this->index]['mappings'][$this->type]) {
            $properties = $mapping['properties'];
            $this->echoProperties($properties);
        }
    }

    protected function echoProperties($properties, $parent = '')
    {
        foreach ($properties as $key => $item) {
            if (isset($item['properties'])) {
                $key .= '.';
                $this->echoProperties($item['properties'], $key);
            } else {
                echo Color::colorize("    字段[{$parent}{$key}] : 类型[{$item['type']}]", Color::FG_LIGHT_BLUE) . PHP_EOL;
            }
        }
    }

    protected function echoCreateIndexMapping()
    {
        $indices = $this->client->indices();

        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'properties' => [
                    'name' => ['type' => 'string'],
                    'book.author' => ['type' => 'string'],
                    'book.name' => ['type' => 'string'],
                    'book.publish' => ['type' => 'date'],
                    'book.desc' => ['type' => 'string'],
                    'age' => ['type' => 'short'],
                    'birthday' => ['type' => 'date'],
                    'location' => ['type' => 'geo_point'],
                    'randnum' => ['type' => 'long'],
                ],
            ],
        ];

        $res = $indices->putMapping($params);

        if ($res['acknowledged']) {
            echo Color::colorize('    Mapping 设置成功', Color::FG_LIGHT_GREEN) . PHP_EOL;
        }
    }

    protected function echoCreateIndex()
    {
        $indices = $this->client->indices();
        $params = [
            'index' => $this->index,
        ];
        try {
            $res = $indices->create($params);
            if ($res['acknowledged']) {
                echo Color::colorize("    索引[{$this->index}] 创建成功", Color::FG_LIGHT_GREEN) . PHP_EOL;
            }
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            echo Color::colorize($res['error']['reason'], Color::FG_LIGHT_RED) . PHP_EOL;
        }
    }

    protected function echoDeleteExistIndex()
    {
        $indices = $this->client->indices();
        $params = [
            'index' => SystemCode::ES_INDEX,
        ];

        if ($indices->exists($params)) {
            $res = $indices->delete($params);
            if ($res['acknowledged']) {
                echo Color::colorize("    删除索引[{$this->index}]成功", Color::FG_LIGHT_RED) . PHP_EOL;
            }
        }
    }

    protected function echoInfo()
    {
        $info = $this->client->info();
        $name = $info['name'];
        $clusterName = $info['cluster_name'];
        $clusterUuid = $info['cluster_uuid'];
        echo Color::colorize("    name: {$name}", Color::FG_LIGHT_BLUE) . PHP_EOL;
        echo Color::colorize("    cluster_name: {$clusterName}", Color::FG_LIGHT_BLUE) . PHP_EOL;
        echo Color::colorize("    cluster_uuid: {$clusterUuid}", Color::FG_LIGHT_BLUE) . PHP_EOL;
    }
}
