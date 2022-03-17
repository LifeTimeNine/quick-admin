<?php

namespace validate;

use basic\Validate;

/**
 * 系统用户验证器
 */
class SystemUser extends Validate
{
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
        'id.require' => '请输入系统用户ID',
        'username.require' => '请输入用户名',
        'username.max' => '用户名超出最大字数限制',
        'avatar.reuqire' => '请选择头像',
        'avatar.url' => '头像地址不正确',
        'name.require' => '请输入用户姓名',
        'name.max' => '用户姓名超出最大字数限制',
        'desc.max' => '用户描述超出最大字数限制',
        'mobile.mobile' => '手机号格式不正确',
        'email.email' => '邮箱格式不正确',
        'srids.require' => '请选择用户角色',
        'srids.array' => '用户角色列表格式不正确',
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
        return $this->only(['avatar','mobile','email']);
    }
}