<?php

namespace App\Tasks\ES;

use App\Logics\EsLogic;
use App\Support\Elasticsearch\Client;
use App\Support\Elasticsearch\ES;
use App\Tasks\Task;
use Xin\Cli\Color;
use Xin\Phalcon\Cli\Traits\Input;

class DocTask extends Task
{
    use Input;

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
        echo Color::colorize('  update              更新文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  geo                 经纬度搜索文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  term                精确查询搜索文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  match               模糊匹配搜索文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  bool                BOOL查询', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  del                 删除文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  delType             删除某类型的所有文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  delQuery            删除某些文档', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  count               某类型文档总数', Color::FG_GREEN) . PHP_EOL;
    }

    public function boolAction()
    {
        $client = Client::getInstance();
        $lat = EsLogic::getRandomLat();
        $lon = EsLogic::getRandomLon();
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['name' => '小王']],
                            ['term' => ['book.author' => 'limx']],
                        ],
                        'filter' => [
                            [
                                'geo_distance' => [
                                    'distance' => '1km',
                                    'location' => [
                                        'lat' => $lat,
                                        'lon' => $lon
                                    ],
                                ]
                            ],
                        ],
                    ],

                ],
                'from' => 0,
                'size' => 5,
                'sort' => [
                    [
                        '_geo_distance' => [
                            'location' => [
                                'lat' => $lat,
                                'lon' => $lon
                            ],
                            'order' => 'asc',
                            'unit' => 'km',
                            'mode' => 'min',
                        ]
                    ],
                    [
                        'randnum' => 'desc',
                    ]
                ],
            ],
        ];
        try {
            $res = $client->search($params);
            dd($res);
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            dd($res);
        }
    }

    public function matchAction()
    {
        $client = Client::getInstance();
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'body' => [
                'query' => [
                    'match' => [
                        'name' => '小王',
                        // 'book.author' => '小王',
                    ]
                ],
                'from' => 0,
                'size' => 5,
                'sort' => [
                    'age' => 'asc'
                ],
            ],
        ];
        try {
            $res = $client->search($params);
            dd($res);
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            dd($res);
        }
    }

    public function termAction()
    {
        $client = Client::getInstance();
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'body' => [
                'query' => [
                    'term' => [
                        'name' => 'limx'
                    ]
                ],
                'from' => 0,
                'size' => 5,
                'sort' => [
                    'age' => 'asc'
                ],
            ],
        ];
        try {
            $res = $client->search($params);
            dd($res);
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            dd($res);
        }
    }

    public function geoAction()
    {
        $client = Client::getInstance();
        $lat = EsLogic::getRandomLat();
        $lon = EsLogic::getRandomLon();
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'geo_distance' => [
                                'distance' => '1km',
                                'location' => [
                                    'lat' => $lat,
                                    'lon' => $lon
                                ],
                            ],
                        ],
                    ]
                ],
                'from' => 0,
                'size' => 5,
                'sort' => [
                    '_geo_distance' => [
                        'location' => [
                            'lat' => $lat,
                            'lon' => $lon
                        ],
                        'order' => 'asc',
                        'unit' => 'km',
                        'mode' => 'min',
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
        }
    }

    public function updateAction()
    {
        $client = Client::getInstance();
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'id' => 101,
            'body' => [
                'doc' => [
                    'name' => '李铭昕',
                    'age' => rand(1, 99),
                    'birthday' => '1990-01-24',
                ]
            ],
            'refresh' => true,
        ];

        try {
            $res = $client->update($params);
            dd($res);
        } catch (\Exception $ex) {
            $res = json_decode($ex->getMessage(), true);
            dd($res);
        }
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
                    // 'match_all' => [
                    //     'boost' => 1.0
                    // ],
                    // 'term' => [
                    //     'name' => '李铭昕0'
                    // ],
                    'bool' => [
                        'filter' => [
                            'geo_distance' => [
                                'distance' => '1km',
                                'location' => [
                                    'lat' => $lat,
                                    'lon' => $lon
                                ],
                            ],
                        ],
                    ]
                ],
                'from' => 0,
                'size' => 5,
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
                        'mode' => 'min',
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
        $length = $this->argument('num') ?? 100;
        $client = Client::getInstance();
        for ($i = 0; $i < $length; $i++) {
            $params = [
                'index' => ES::ES_INDEX,
                'type' => ES::ES_TYPE_USER,
                'id' => $i,
                'body' => [
                    'name' => EsLogic::getRandomName(),
                    'age' => rand(1, 99),
                    'birthday' => EsLogic::getRandomDate(),
                    'book' => [
                        'author' => EsLogic::getRandomName(),
                        'name' => EsLogic::getRandomBook(),
                        'publish' => EsLogic::getRandomDate(),
                        'desc' => EsLogic::getRandomBook() . ' 不在话下。'
                    ],
                    'location' => [
                        'lat' => EsLogic::getRandomLat(),
                        'lon' => EsLogic::getRandomLon(),
                    ],
                    'randnum' => rand(1, 999999)
                ],
            ];
            // dd($params);
            try {
                $res = $client->index($params);
                if ($res['created']) {
                    echo Color::colorize('用户DOC创建成功', Color::FG_GREEN) . PHP_EOL;
                } else {
                    echo Color::colorize('用户DOC创建失败（可能已存在））', Color::FG_LIGHT_RED) . PHP_EOL;
                }
            } catch (\Exception $ex) {
                dd($ex->getMessage());
            }
        }

    }

    public function delTypeAction()
    {
        $client = Client::getInstance();
        try {
            $params = [
                'index' => ES::ES_INDEX,
                'type' => ES::ES_TYPE_USER,
                'body' => [
                    'query' => [
                        'match_all' => (object)[]
                    ],
                ],
            ];
            $res = $client->deleteByQuery($params);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
        dd($res);
    }

    public function delAction()
    {
        $id = $this->argument('id', 1);
        $client = Client::getInstance();
        $params = [
            'index' => ES::ES_INDEX,
            'type' => ES::ES_TYPE_USER,
            'id' => $id,
        ];
        try {
            $res = $client->get($params);
        } catch (\Exception $ex) {
            EsLogic::add($id);
        }

        try {
            $res = $client->delete($params);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }

        dd($res);
    }

    public function delQueryAction()
    {
        $client = Client::getInstance();
        try {
            $params = [
                'index' => ES::ES_INDEX,
                'type' => ES::ES_TYPE_USER,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['book.author' => 'limx']]
                            ],
                        ]
                    ],
                ],
            ];
            $res = $client->deleteByQuery($params);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
        dd($res);
    }

    public function countAction()
    {
        $client = Client::getInstance();
        try {
            $params = [
                'index' => ES::ES_INDEX,
                'type' => ES::ES_TYPE_USER,
            ];
            $res = $client->count($params);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
        }
        dd($res);
    }

}

