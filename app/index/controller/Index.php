<?php
namespace app\index\controller;

use basic\Controller;
use service\Code;

class Index extends Controller
{
    public function index()
    {
        $this->return(Code::DATA_NOT_EXIST);
        echo "Quick admin";
    }

    public function test()
    {
    }
}

