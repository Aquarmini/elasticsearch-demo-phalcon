<?php

namespace App\Tasks\ES;

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

    }

    public function addAction()
    {
        echo 1;
    }

}

