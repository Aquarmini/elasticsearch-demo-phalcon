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
[代码目录](https://github.com/Aquarmini/elasticsearch-demo-phalcon/tree/demo/app/tasks/ES)

~~~
# 查看文档目录
php run es:main
~~~

## 注意事项
* sort关键字，bool.must、bool.filter ... 里需要插入数组 例如：
~~~
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
                    ['geo_distance' => [
                        'distance' => '1km',
                        'location' => [
                            'lat' => $lat,
                            'lon' => $lon
                        ],
                    ]],
                ],
            ],

        ],
        'from' => 0,
        'size' => 5,
        'sort' => [
            ['_geo_distance' => [
                'location' => [
                    'lat' => $lat,
                    'lon' => $lon
                ],
                'order' => 'asc',
                'unit' => 'km',
                'mode' => 'min',
            ]],
            [
                'randnum' => 'desc',
            ]
        ],
    ],
];
~~~

