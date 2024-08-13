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
    protected $createTime = 'first_happen_time';
    protected $updateTime = false;

    protected $type = [
        'request_param' => ['json', JSON_FORCE_OBJECT],
        'header' => ['json', JSON_FORCE_OBJECT],
        'session' => ['json', JSON_FORCE_OBJECT]
    ];

    /**
     * 处理用户
     */
    public function resolveUser()
    {
        return $this->belongsTo(SystemUser::class, 'resolve_suid');
    }
}