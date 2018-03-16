<?php
// +----------------------------------------------------------------------
// | 基础测试类 [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace Tests\Units;

use App\Common\Clients\ElasticSearchClient;
use App\Common\Clients\Rpc\BasicClient;
use App\Common\Enums\SystemCode;
use Tests\UnitTestCase;

/**
 * Class UnitTest
 */
class ElasticSearchTest extends UnitTestCase
{
    public function testGetDocumnet()
    {
        $client = ElasticSearchClient::getInstance();
        $params = [
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'id' => 0,
        ];
        $res = $client->get($params);

        $this->assertTrue($res['found']);
        $source = $res['_source'];

        $expect = di('configCenter')->get('es_docs')->toArray()[0];
        $this->assertEquals($expect, $source);
    }

    public function testAddAndDeleteDocument()
    {
        $client = ElasticSearchClient::getInstance();
        $body = [
            'name' => 'Mr.999',
            'age' => 99,
            'birthday' => '1990-01-01',
            'book' => [
                'author' => 'limx',
                'name' => '长寿的秘诀',
                'publish' => '2018-01-01',
                'desc' => '多睡觉'
            ],
            'location' => [
                'lat' => 0,
                'lon' => 0,
            ],
            'randnum' => 999
        ];

        $params = [
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'id' => 999,
            'body' => $body,
        ];

        $res = $client->index($params);

        $this->assertTrue($res['created']);
        $this->assertEquals('created', $res['result']);

        $doc = $client->get([
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'id' => 999,
        ]);

        $this->assertTrue($doc['found']);
        $this->assertEquals($body, $doc['_source']);

        // 编辑
        $body['age'] = 100;
        $params = [
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'id' => 999,
            'body' => [
                'doc' => [
                    'age' => 100,
                ],
                'doc_as_upsert' => true,
            ],
            'refresh' => true,
        ];

        $res = $client->update($params);

        $this->assertEquals('updated', $res['result']);

        $doc = $client->get([
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'id' => 999,
        ]);

        $this->assertTrue($doc['found']);
        $this->assertEquals($body, $doc['_source']);

        // 删除
        $res = $client->delete([
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'id' => 999,
        ]);

        $this->assertTrue($res['found']);
        $this->assertEquals('deleted', $res['result']);
        $this->assertEquals(1, $res['_shards']['successful']);
        $this->assertEquals(0, $res['_shards']['failed']);
    }

    public function testGeoBoolQuery()
    {
        $client = ElasticSearchClient::getInstance();
        $lat = 31.249162;
        $lon = 121.487899;

        $params = [
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
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

        $docs = di('configCenter')->get('es_docs')->toArray();
        $expect = [];
        $expect[] = $docs[0];
        $expect[] = $docs[3];
        $expect[] = $docs[1];

        $res = $client->search($params);
        $this->assertEquals(3, $res['hits']['total']);

        $actual = [];
        foreach ($res['hits']['hits'] as $item) {
            $actual[] = $item['_source'];
        }

        $this->assertEquals($expect, $actual);
    }

    public function testBoolQuery()
    {
        $client = ElasticSearchClient::getInstance();
        $lat = 31.249162;
        $lon = 121.487899;
        $params = [
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['book.desc' => '学会']],
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
                        'randnum' => 'desc',
                    ]
                ],
            ],
        ];

        $docs = di('configCenter')->get('es_docs')->toArray();
        $expect = [];
        $expect[] = $docs[3];
        $expect[] = $docs[0];

        $res = $client->search($params);
        $this->assertEquals(2, $res['hits']['total']);

        $actual = [];
        foreach ($res['hits']['hits'] as $item) {
            $actual[] = $item['_source'];
        }

        $this->assertEquals($expect, $actual);
    }

    public function testOrBoolQuery()
    {
        $client = ElasticSearchClient::getInstance();
        $params = [
            'index' => SystemCode::ES_INDEX,
            'type' => SystemCode::ES_TYPE,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['match' => ['book.desc' => ['query' => '学会']]],
                            ['match' => ['book.name' => '精通']],
                        ],
                    ],

                ],
                'from' => 0,
                'size' => 5,
                'sort' => [
                    [
                        'randnum' => 'desc',
                    ]
                ],
            ],
        ];

        $res = $client->search($params);
        $this->assertEquals(4, $res['hits']['total']);
    }
}
