<?php

namespace model;

use basic\Model;

/**
 * 系统操作记录模型
 */
class SystemActionLog extends Model
{
    protected $pk = 'id';
    protected $table = 'system_action_log';

    protected $json = ['request_param'];
    protected $jsonAssoc = true;

    /**
     * 用户信息
     */
    public function systemUser()
    {
        return $this->belongsTo(SystemUser::class, 'suid', 'id');
    }
}