<?php

namespace model;

use basic\Model;

/**
 * 系统任务执行日志
 */
class SystemTaskLog extends Model
{
    protected $pk = 'id';
    protected $table = 'system_task_log';
}