<?php

namespace model;

use basic\Model;

/**
 * 系统异常记录模型
 */
class SystemErrorLog extends Model
{
    protected $pk = 'id';
    protected $table = 'system_error_log';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'happen_time';
    protected $updateTime = false;

    protected $json = ['request_param'];
    protected $jsonAssoc = true;

    /**
     * 处理用户
     */
    public function resolveUser()
    {
        return $this->belongsTo(SystemUser::class, 'resolve_suid');
    }
}