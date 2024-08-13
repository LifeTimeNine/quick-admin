<?php
namespace app\index\controller;

use Exception;
use service\Node;
use service\SystemConfig;
use service\SystemTask;
use service\Token;
use think\facade\Event;
use tools\Query;
use tools\Request;
use traits\controller\QuickAction;

class Index
{
    public function index()
    {
        throw new \Exception('test', 100);
        // dump(Event::until('system.exception', new \Exception('test')));
        // dump(app()->isDebug());
    }

    public function test()
    {
    }
}

