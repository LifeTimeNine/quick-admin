<?php

namespace basic;

use traits\model\Tools;

/**
 * 模型基类
 */
abstract class Model extends \think\Model
{
    use Tools;

    protected $pk = 'id';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $jsonAssoc = true;
}