<?php

namespace validate;

use lang\Variable;
use basic\Validate;

/**
 * 系统角色验证器
 */
class SystemRole extends Validate
{
    protected $model = \model\SystemRole::class;

    protected $rule = [
        'id' => 'require',
        'name' => 'require|max:64',
        'desc' => 'max:200',
    ];

    protected $message = [
        'id.require' => Variable::REQUIRED,
        'name.require' => Variable::REQUIRED,
        'name.max' => Variable::MAXIMUN_WORD_LIMIT,
        'desc.max' => Variable::MAXIMUN_WORD_LIMIT,
    ];

    /**
     * 新增
     */
    protected function sceneAdd()
    {
        return $this->remove('id', true);
    }
    /**
     * 编辑
     */
    protected function sceneEdit()
    {
        return $this;
    }
}