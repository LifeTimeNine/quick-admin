<?php

namespace validate;

use basic\Validate;

/**
 * 系统菜验证器
 */
class SystemMenu extends Validate
{
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
        'id.require' => '请输入系统菜单ID',
        'pid.require' => '请选择父级菜单',
        'title.require' => '请输入标题',
        'title.max' => '标题超出最大字数限制',
        'icon.max' => '图标超出最大字数限制',
        'url.require' => '请输入页面地址',
        'url.max' => '页面地址超出最大字数限制',
        'node' => '访问节点超出最大字数限制',
        'params' => '参数超出最大字数限制',
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