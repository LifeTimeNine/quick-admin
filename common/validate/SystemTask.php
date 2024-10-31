<?php

namespace validate;

use basic\Validate;
use lang\Variable;

/**
 * 系统任务验证器
 */
class SystemTask extends Validate
{
    protected $model = \model\SystemTask::class;
    protected $rule = [
        'id' => 'require',
        'title' => 'require|max:100',
        'exec_file' => 'require|max:1000',
        'args' => 'max:1000',
        'type' => 'require|in:1,2',
        'cron' => 'requireIf:type,1|max:200'
    ];
    protected $message = [
        'id.require' => Variable::REQUIRED,
        'title.require' => Variable::REQUIRED,
        'title.max' => Variable::MAXIMUM_WORD_LIMIT,
        'exec_file.require' => Variable::REQUIRED,
        'exec_file.max' => Variable::MAXIMUM_WORD_LIMIT,
        'args.max' => Variable::MAXIMUM_WORD_LIMIT,
        'type.require' => Variable::REQUIRED,
        'type.in' => Variable::TYPE_ILLEGAL,
        'cron.requireIf' => Variable::REQUIRED,
        'cron.max' => Variable::MAXIMUM_WORD_LIMIT,
    ];

    protected function sceneAdd()
    {
        return $this->remove('id', true);
    }
}