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
        $tokenService = Token::instance();
        $tokens = $tokenService->build(['id' => 1]);
        dump($tokens);
        dump($tokenService->parse($tokens['access_token'] . '1'));
        dump($tokenService->getError());
        dump(app()->isDebug());
    }

    public function test()
    {
    }
}

