<?php

namespace App\Tasks;

use Xin\Cli\Color;

class InitTask extends Task
{
    protected $index = 'es:test';

    public function mainAction()
    {
        echo Color::head('初始化ES') . PHP_EOL;
    }
}
