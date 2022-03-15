<?php
namespace app\index\controller;

use service\Node;
use service\SystemConfig;
use service\SystemTask;
use service\Token;
use tools\Query;
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

