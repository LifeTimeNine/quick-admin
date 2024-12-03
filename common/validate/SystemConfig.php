<?php

namespace validate;

use lang\Variable;
use basic\Validate;
use model\SystemConfig as SystemConfigModel;

/**
 * 系统配置验证器
 */
class SystemConfig extends Validate
{
    protected $model = SystemConfigModel::class;
    protected $rule = [
        'id' => 'require',
        'key' => 'require|max:100',
        'type' => 'require',
        'name' => 'require|max:200'
    ];

    protected $message = [
        'id.require' => Variable::REQUIRED,
        'key.require' => Variable::REQUIRED,
        'key.max' => Variable::MAXIMUM_WORD_LIMIT,
        'key.unique' => Variable::HAS_EXIST,
        'type.require' => Variable::REQUIRED,
        'type.in' => Variable::TYPE_ILLEGAL,
        'name.require' => Variable::REQUIRED,
        'name.max' => Variable::MAXIMUM_WORD_LIMIT,
    ];

    protected function sceneAdd()
    {
        return $this->remove('id', true)
            ->append('type', 'in:' . implode(',', [
                SystemConfigModel::TYPE_TEXT,
                SystemConfigModel::TYPE_LIST,
                SystemConfigModel::TYPE_MAP,
                SystemConfigModel::TYPE_IMG
            ]))
            ->append('key', 'unique:' . SystemConfigModel::getTableName());
    }
    protected function sceneEdit()
    {
        return $this->remove('key', true);
    }
}
