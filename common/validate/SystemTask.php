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
        'command' => 'require|max:1000',
        'params' => 'max:1000',
        'type' => 'require|in:1,2',
        'crontab' => 'requireIf:type,1|max:200'
    ];
    protected $message = [
        'id.require' => Variable::REQUIRED,
        'title.require' => Variable::REQUIRED,
        'title.max' => Variable::MAXIMUN_WORD_LIMIT,
        'command.require' => Variable::REQUIRED,
        'command.max' => Variable::MAXIMUN_WORD_LIMIT,
        'params.max' => Variable::MAXIMUN_WORD_LIMIT,
        'type.require' => Variable::REQUIRED,
        'type.in' => Variable::TYPE_ILLEGAL,
        'crontab.requireIf' => Variable::REQUIRED,
        'crontab.max' => Variable::MAXIMUN_WORD_LIMIT,
    ];

    protected function sceneAdd()
    {
        return $this->remove('id', true);
    }
}