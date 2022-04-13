<?php

namespace basic;

use traits\model\Tools;

/**
 * 多对多中间表模型基类
 */
class Pivot extends \think\model\Pivot
{
    use Tools;

    protected $pk = 'id';
}