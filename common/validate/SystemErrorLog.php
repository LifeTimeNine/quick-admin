<?php

declare (strict_types = 1);

namespace validate;

use basic\Validate;
use lang\Variable;

/**
 * 系统异常记录验证器
 */
class SystemErrorLog extends Validate
{
    protected $model = \model\SystemErrorLog::class;
    protected $rule = [
        'resolve_remark' => 'require|max:500'
    ];

    protected $message = [
        'resolve_remark.require' => Variable::REQUIRED,
        'resolve_remark.max' => Variable::MAXIMUM_WORD_LIMIT
    ];

    /**
     * 处理
     */
    protected function sceneResolve()
    {
        return $this->only(['resolve_remark']);
    }
}