<?php

namespace validate;

use basic\Validate;
use model\SystemConfig as SystemConfigModel;

/**
 * 系统配置验证器
 */
class SystemConfig extends Validate
{
    protected $rule = [
        'key' => 'require|max:100',
        'type' => 'require|in:1,2,3',
        'name' => 'require|max:200'
    ];

    protected $message = [
        'key.require' => '请输入配置键',
        'key.max' => '配置键超出最大字数限制',
        'key.unique' => '配置键已存在',
        'type.require' => '请选择配置类型',
        'type.in' => '配置类型不合法',
        'name.require' => '请输入配置名称',
        'name.max' => '配置名称超出最大字数限制'
    ];

    
    protected function sceneAdd()
    {
        return $this->append('key', 'unique:' . SystemConfigModel::getTableName());
    }
}