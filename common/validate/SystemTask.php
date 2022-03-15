<?php

namespace validate;

use basic\Validate;

/**
 * 系统任务验证器
 */
class SystemTask extends Validate
{
    protected $rule = [
        'id' => 'require',
        'title' => 'require|max:100',
        'command' => 'require|max:1000',
        'params' => 'max:1000',
        'type' => 'require|in:1,2',
        'crontab' => 'requireIf:type,1|max:200'
    ];
    protected $message = [
        'id.require' => '请输入ID',
        'title.require' => '请输入任务名称',
        'title.max' => '任务名称超出最大字数限制',
        'command.require' => '请输入指令',
        'command.max' => '指令超出最大字数限制',
        'params.max' => '参数超出最大字数限制',
        'type.require' => '请选择任务类型',
        'type.in' => '类型值不合法',
        'crontab.requireIf' => '请输入定时参数',
        'crontab.max' => '定时参数超出最大字数限制'
    ];

    protected function sceneAdd()
    {
        return $this->remove('id', true);
    }
}