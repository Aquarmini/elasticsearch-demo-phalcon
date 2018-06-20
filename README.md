# PHALCON基础开发框架

> 本项目以[limingxinleo/phalcon-project](https://github.com/limingxinleo/phalcon)为基础，进行简易封装。

[![Build Status](https://travis-ci.org/Aquarmini/elasticsearch-demo-phalcon.svg?branch=master)](https://travis-ci.org/Aquarmini/elasticsearch-demo-phalcon)
[![Total Downloads](https://poser.pugx.org/limingxinleo/phalcon-basic-project/downloads)](https://packagist.org/packages/limingxinleo/phalcon-basic-project)
[![Latest Stable Version](https://poser.pugx.org/limingxinleo/phalcon-basic-project/v/stable)](https://packagist.org/packages/limingxinleo/phalcon-basic-project)
[![Latest Unstable Version](https://poser.pugx.org/limingxinleo/phalcon-basic-project/v/unstable)](https://packagist.org/packages/limingxinleo/phalcon-basic-project)
[![License](https://poser.pugx.org/limingxinleo/phalcon-basic-project/license)](https://packagist.org/packages/limingxinleo/phalcon-project)


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

## 安装
~~~bash
$ composer create-project limingxinleo/phalcon-basic-project
~~~

## 扩展介绍

[limingxinleo/phalcon-project-library](https://github.com/limingxinleo/phalcon-project-library)

框架所用的基础扩展

[limingxinleo/x-swoole-rpc](https://github.com/limingxinleo/x-swoole-rpc)

用户内部服务通信的RPC扩展，基于Swoole Tcp Server & Client 开发。期待Swoole4.0协程时代的到来。

[limingxinleo/x-phalcon-config-center](https://github.com/limingxinleo/x-phalcon-config-center)

配置中心模块，方便配置文件分类读取

[limingxinleo/x-phalcon-enum](https://github.com/limingxinleo/x-phalcon-enum)

基于注解的枚举类

## 封装版本
- [Thrift GO服务版本](https://github.com/limingxinleo/thrift-go-phalcon-project)
- [Phalcon快速开发框架](https://github.com/limingxinleo/biz-phalcon)
- [Phalcon基础开发框架](https://github.com/limingxinleo/basic-phalcon)
- [Zipkin开发版本](https://github.com/limingxinleo/zipkin-phalcon)
- [Eureka开发版本](https://github.com/limingxinleo/eureka-phalcon)
- [RabbitMQ](https://github.com/limingxinleo/rabbitmq-phalcon)
- [ELK开发版本](https://github.com/limingxinleo/elk-phalcon)
- [配置中心](https://github.com/limingxinleo/config-center-phalcon)

## 测试以及其他DEMO
- [框架测试](https://github.com/limingxinleo/phalcon-unit-test)
- [多库单表](https://github.com/limingxinleo/service-demo-order)
- [Elasticsearch](https://github.com/Aquarmini/elasticsearch-demo-phalcon)
- [kafka](https://github.com/Aquarmini/kafka-demo-phalcon)
- [机器学习](https://github.com/Aquarmini/ml-demo-phalcon)
- [正则匹配](https://github.com/Aquarmini/regex-demo-phalcon)


