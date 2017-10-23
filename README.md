# phalcon-project
[![Total Downloads](https://poser.pugx.org/limingxinleo/phalcon-project/downloads)](https://packagist.org/packages/limingxinleo/phalcon-project)
[![Latest Stable Version](https://poser.pugx.org/limingxinleo/phalcon-project/v/stable)](https://packagist.org/packages/limingxinleo/phalcon-project)
[![Latest Unstable Version](https://poser.pugx.org/limingxinleo/phalcon-project/v/unstable)](https://packagist.org/packages/limingxinleo/phalcon-project)
[![License](https://poser.pugx.org/limingxinleo/phalcon-project/license)](https://packagist.org/packages/limingxinleo/phalcon-project)


[Phalcon 官网](https://docs.phalconphp.com/zh/latest/index.html)

[wiki](https://github.com/limingxinleo/simple-subcontrollers.phalcon/wiki)

## Elasticsearch使用

### 安装
~~~
composer require elasticsearch/elasticsearch
~~~

### 使用
1. 索引初始化 - 新建索引
~~~
$client = ClientBuilder::create()->setHosts([$host])->build();
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
~~~

2. 索引初始化 - 初始化Mapping
[types](https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html)
~~~
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
~~~

3. 索引初始化 - 读取索引
~~~
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
~~~

4. 使用 - 插入数据
~~~
$client = Client::getInstance();
$lat = 31.249162;
$lon = 121.487899;
for ($i = 0; $i < 10; $i++) {
    $num = $i + rand(1, 1000);
    $vlat = bcadd($lat, $num / 100000, 6);
    $vlon = bcadd($lon, $num / 100000, 6);
    $params = [
        'index' => ES::ES_INDEX,
        'type' => ES::ES_TYPE_USER,
        'id' => $i,
        'body' => [
            'name' => '李铭昕' . $i,
            'age' => 24,
            'birthday' => '1990-01-23',
            'location' => [
                'lat' => $vlat,
                'lon' => $vlon,
            ],
        ],
    ];
    $res = $client->index($params);
    if ($res['created']) {
        echo Color::colorize('用户DOC创建成功', Color::FG_GREEN) . PHP_EOL;
    } else {
        echo Color::colorize('用户DOC创建失败（可能已存在））', Color::FG_LIGHT_RED) . PHP_EOL;
    }
}
~~~

5. 使用 - 读取文档
~~~
$client = Client::getInstance();
$params = [
    'index' => ES::ES_INDEX,
    'type' => ES::ES_TYPE_USER,
    'id' => 0,
];
$res = $client->get($params);
dd($res);
~~~

6. 使用 - 搜索文档
~~~
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
~~~