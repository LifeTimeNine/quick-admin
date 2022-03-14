<?php

namespace model;

use think\model\Pivot;

/**
 * 系统用户角色模型
 */
class SystemUserRole extends Pivot
{
    protected $pk = 'id';
    protected $table = 'system_user_role';
}