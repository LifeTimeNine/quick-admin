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
    protected $model = \model\SystemConfig::class;
    protected $rule = [
        'id' => 'require',
        'key' => 'require|max:100',
        'type' => 'require|in:1,2,3',
        'name' => 'require|max:200'
    ];

    protected $message = [
        'id.require' => Variable::REQUIRED,
        'key.require' => Variable::REQUIRED,
        'key.max' => Variable::MAXIMUN_WORD_LIMIT,
        'key.unique' => Variable::HAS_EXIST,
        'type.require' => Variable::REQUIRED,
        'type.in' => Variable::TYPE_ILLEGAL,
        'name.require' => Variable::REQUIRED,
        'name.max' => Variable::MAXIMUN_WORD_LIMIT,
    ];

    protected function sceneAdd()
    {
        return $this->remove('id', true)
            ->append('key', 'unique:' . SystemConfigModel::getTableName());
    }
    protected function sceneEdit()
    {
        return $this->remove('key', true);
    }
}
