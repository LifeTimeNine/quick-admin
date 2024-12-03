<?php

namespace model;

use basic\Model;
use think\model\concern\SoftDelete;
use traits\model\ScopeStatus;

/**
 * 系统角色表
 * @method static $this withTrashed()
 * @method static $this onlyTrashed()
 */
class SystemRole extends Model
{
    protected $pk = 'id';
    protected $table = 'system_role';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    use SoftDelete;
    protected $deleteTime = 'delete_time';

    protected $hidden=['pivot'];

    use ScopeStatus;
}