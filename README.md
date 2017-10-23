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
