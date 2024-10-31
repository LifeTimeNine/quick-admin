<?php

namespace model;

use basic\Model;
use traits\model\ScopeStatus;

/**
 * 系统任务模型
 */
class SystemTask extends Model
{
    use ScopeStatus;

    protected $pk = 'id';
    protected $table = 'system_task';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    /**
     * 类型 定时
     */
    const TYPE_TIMING = 1;
    /**
     * 类型 手动
     */
    const TYPE_MANUAL = 2;

    /**
     * 执行状态列表
     * @var array
     */
    public static $execStatusList = [
        1 => '等待中',
        2 => '执行中'
    ];

}