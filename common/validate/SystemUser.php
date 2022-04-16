<?php

namespace validate;

use basic\Validate;
use lang\Variable;
use model\SystemUser as SystemUserModel;

/**
 * 系统用户验证器
 */
class SystemUser extends Validate
{
    protected $model = \model\SystemUser::class;
    protected $rule = [
        'id' => 'require',
        'username' => 'require|max:64',
        'avatar' => 'require|url',
        'name' => 'require|max:32',
        'desc' => 'max:200',
        'mobile' => 'mobile',
        'email' => 'email',
        'rids' => 'require|array',
    ];
    protected $message = [
        'id.require' => Variable::REQUIRED,
        'username.require' => Variable::REQUIRED,
        'username.max' => Variable::MAXIMUN_WORD_LIMIT,
        'avatar.reuqire' => Variable::REQUIRED,
        'avatar.url' => Variable::URL_NOT_CORRECT,
        'name.require' => Variable::REQUIRED,
        'name.max' => Variable::MAXIMUN_WORD_LIMIT,
        'desc.max' => Variable::MAXIMUN_WORD_LIMIT,
        'mobile.mobile' => Variable::FORMAT_CORRECT,
        'mobile.unique' => Variable::HAS_EXIST,
        'email.email' => Variable::FORMAT_CORRECT,
        'email.unique' => Variable::HAS_EXIST,
        'srids.require' => Variable::REQUIRED,
        'srids.array' => Variable::FORMAT_CORRECT,
    ];

    /**
     * 新增
     */
    protected function sceneAdd()
    {
        return $this->remove('id', true)
            ->remove('avatar', true)
            ->remove('mobile', true)
            ->remove('email', true);
    }
    /**
     * 编辑
     */
    protected function sceneEdit()
    {
        return $this->remove('username', true)
            ->remove('avatar', true)
            ->remove('mobile', true)
            ->remove('email', true);
    }
    /**
     * 用户编辑
     */
    protected function sceneUserEdit()
    {
        return $this->only(['avatar','mobile','email'])
            ->append('mobile', 'unique:'. SystemUserModel::class)
            ->append('email', 'unique:' . SystemUserModel::class);
    }
}