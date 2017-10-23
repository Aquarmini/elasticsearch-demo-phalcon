<?php

namespace App\Tasks\ES;

use App\Support\Elasticsearch\Client;
use App\Support\Elasticsearch\ES;
use App\Tasks\Task;
use Xin\Cli\Color;

class DocTask extends Task
{

    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  ElasticSearch测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run es:doc@[action]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Actions:') . PHP_EOL;
        echo Color::colorize('  add                 插入文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  get                 读取文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  search              搜索文档', Color::FG_GREEN) . PHP_EOL;

    }

    public function searchAction()
    {
        $client = Client::getInstance();
        $lat = 31.249162;
        $lon = 121.487899;
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'body' => [
                'query' => [
                    // 'match' => [
                    //     'name' => [
                    //         'query' => '1'
                    //     ]
                    // ],
                    'match_all' => [],
                    // 'term' => [
                    //     'name' => '李铭昕0'
                    // ],

                ],
                'filter' => [
                    'geo_distance' => [
                        'distance' => '1km',
                        'location' => [
                            'lat' => $lat,
                            'lon' => $lon
                        ],
                    ],
                ],
                // 'script_fields' => [
                //     'distance' => [
                //         'params' => [
                //             'lat' => $lat,
                //             'lon' => $lon
                //         ],
                //         'script' => "doc['location'].distanceInKm(lat,lon)",
                //     ],
                // ],
                'from' => 0,
                'size' => 2,
                'sort' => [
                    // 'age' => [
                    //     'order' => 'desc'
                    // ]
                    '_geo_distance' => [
                        'location' => [
                            'lat' => $lat,
                            'lon' => $lon
                        ],
                        'order' => 'asc',
                        'unit' => 'km',
                    ],
                ],
            ],
        ];
        try {
            $res = $client->search($params);
            dd($res);
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            dd($res);
            if ($res) {
                echo Color::colorize($res['error']['reason'], Color::FG_LIGHT_RED) . PHP_EOL;
            } else {
                echo Color::colorize($ex->getMessage(), Color::FG_LIGHT_RED) . PHP_EOL;
            }
        }
    }

    public function getAction()
    {
        $client = Client::getInstance();
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'id' => 1,
        ];
        $res = $client->get($params);
        dd($res);
    }

    public function addAction()
    {
        $client = Client::getInstance();
        $lat = 31.249162;
        $lon = 121.487899;
        for ($i = 0; $i < 100; $i++) {
            $num = $i + rand(1, 1000);
            $vlat = bcadd($lat, $num / 100000, 6);
            $vlon = bcadd($lon, $num / 100000, 6);
            $params = [
                'index' => ES::ES_INDEX,
                'type' => ES::ES_TYPE_USER,
                'id' => $i,
                'body' => [
                    'name' => '李铭昕' . $i,
                    'age' => rand(1, 99),
                    'birthday' => '1990-01-23',
                    'location' => [
                        'lat' => $vlat,
                        'lon' => $vlon,
                    ],
                    'randnum' => rand(1, 999999)
                ],
            ];
            $res = $client->index($params);
            if ($res['created']) {
                echo Color::colorize('用户DOC创建成功', Color::FG_GREEN) . PHP_EOL;
            } else {
                echo Color::colorize('用户DOC创建失败（可能已存在））', Color::FG_LIGHT_RED) . PHP_EOL;
            }
        }

    }

}

