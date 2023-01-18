<?php

namespace validate;

use basic\Validate;
use lang\Variable;

/**
 * 系统菜验证器
 */
class SystemMenu extends Validate
{
    protected $model = \model\SystemMenu::class;
    protected $rule = [
        'id' => 'require',
        'pid' => 'require',
        'title' => 'require|max:64',
        'icon' => 'max:128',
        'url' => 'require|max:200',
        'node' => 'max:200',
        'params' => 'max:200',
    ];

    protected  $message = [
        'id.require' => Variable::REQUIRED,
        'pid.require' => Variable::REQUIRED,
        'title.require' => Variable::REQUIRED,
        'title.max' => Variable::MAXIMUM_WORD_LIMIT,
        'icon.max' => Variable::MAXIMUM_WORD_LIMIT,
        'url.require' => Variable::REQUIRED,
        'url.max' => Variable::MAXIMUM_WORD_LIMIT,
        'node.max' => Variable::MAXIMUM_WORD_LIMIT,
        'params.max' => Variable::MAXIMUM_WORD_LIMIT,
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