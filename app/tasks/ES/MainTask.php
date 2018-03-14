<?php

namespace App\Tasks\ES;

use App\Tasks\Task;
use Xin\Cli\Color;

class MainTask extends Task
{
    public function mainAction()
    {
        echo Color::head('Help:') . PHP_EOL;
        echo Color::colorize('  ElasticSearch测试') . PHP_EOL . PHP_EOL;

        echo Color::head('Usage:') . PHP_EOL;
        echo Color::colorize('  php run es:[module]', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        echo Color::head('Module:') . PHP_EOL;
        echo Color::colorize('  index           索引操作', Color::FG_GREEN) . PHP_EOL;
        echo Color::colorize('  doc             文档操作', Color::FG_GREEN) . PHP_EOL;
    }
}
