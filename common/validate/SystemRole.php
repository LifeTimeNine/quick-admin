<?php

namespace validate;

use basic\Validate;

/**
 * 系统角色验证器
 */
class SystemRole extends Validate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|max:64',
        'desc' => 'max:200',
    ];

    protected $message = [
        'id.require' => '请输入系统角色ID',
        'name.require' => '请输入名称',
        'name.max' => '名称超出最大字数限制',
        'desc.max' => '描述超出最大字数限制',
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