<?php
namespace app\index\controller;

use service\Node;
use service\SystemConfig;
use service\SystemTask;
use tools\Request;
use traits\controller\QuickAction;

class Index
{
    public function index()
    {
        dump(app()->isDebug());
    }

    public function test()
    {
    }
}

