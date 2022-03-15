<?php

namespace model;

use basic\Model;
use think\model\concern\SoftDelete;
use traits\model\ScopeStatus;

/**
 * 系统菜单模型
 */
class SystemMenu extends Model
{
    protected $pk = 'id';
    protected $table = 'system_menu';

    protected $autoWriteStamptime = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    use SoftDelete;
    protected $deleteTime = 'delete_time';

    use ScopeStatus;
}