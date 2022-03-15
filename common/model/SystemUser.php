<?php

namespace model;

use basic\Model;
use think\model\concern\SoftDelete;

/**
 * 系统用户模型
 */
class SystemUser extends Model
{
    protected $pk = 'id';
    protected $table = 'system_user';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    use SoftDelete;
    protected $deleteTime = 'delete_time';

    /**
     * ip 修改器
     */
    public function setLastLoginIpAttr($value)
    {
        return ip2long($value);
    }
    /**
     * ip 获取器
     */
    public function getLastLoginIpAttr($value)
    {
        return empty($value) ? $value : long2ip($value);
    }
    /**
     * 头像获取器
     */
    public function getAvatarAttr($value)
    {
        return $value ?? 'http://localhost/static/img/avatar.gif';
    }

    /**
     * 角色信息
     */
    public function roles()
    {
        return $this->belongsToMany(SystemRole::class, SystemUserRole::class, 'srid', 'suid');
    }
}