<?php

use lang\Variable;

return [
    /** 异常Code 消息 */
    Variable::SUCCESS => '成功',
    Variable::ERROR => '错误',
    Variable::PARAM_ERROR => '参数异常',
    Variable::ACTION_FAIL => '操作失败',
    Variable::DATA_NOT_EXIST => '数据不存在',
    Variable::TOKEN_ERROR => '身份信息验证失败请重新登录',
    Variable::TOKEN_EXPIRE => '身份信息已过期请重新登录',
    Variable::TOKEN_REFRESH_FAIL => '身份信息刷新失败',
    Variable::TOKEN_FAILURE => '身份信息已失效请重新登录',
    Variable::USER_DISABLE => '账户已被禁用',
    Variable::USER_LOGIN => '该账户已在其他地点被登录',
    Variable::PERMISSION_DENIED => '权限不足，无法访问此功能',

    Variable::USERNAME_OR_PASSWORD_NOT_CORRECT => '用户名或密码不正确',
    Variable::OLD_PASSWORD_NOT_CORRECT => '原密码不正确',
    Variable::ENTERED_PASSWORDS_DIFFER => '两次输入的密码不一致',

    Variable::FILE_NOT_EXIST => '文件不存在',
    Variable::FILE_COMPLETE_FAil => '文件合并失败',
    Variable::FILE_TYPE_NOT_ALLOWED => '不允许上传此类文件',

    Variable::REQUIRED => '请输入:attribute',
    Variable::MAXIMUM_WORD_LIMIT => ':attribute超出最大字数限制',
    Variable::HAS_EXIST => ':attribute已存在',
    Variable::TYPE_ILLEGAL => ':attribute类型不合法',
    Variable::URL_NOT_CORRECT => ':attribute地址不正确',
    Variable::FORMAT_CORRECT => ':attribute格式不正确',

    /** 数据表字段 */
    'system_config' => [
        'id' => '系统配置ID',
        'key' => '配置键',
        'name' => '配置名称'
    ],
    'system_role' => [
        'id' => '系统角色ID',
        'name' => '系统角色名称',
        'desc' => '系统角色描述',
    ],
    'system_menu' => [
        'id' => '系统菜单ID',
        'pid' => '父级菜单',
        'title' => '标题',
        'icon' => '图标',
        'url' => '页面地址',
        'node' => '权限节点',
        'params' => '参数'
    ],
    'system_task' => [
        'id' => '系统任务ID',
        'title' => '任务名称',
        'command' => '任务指令',
        'params' => '任务参数',
        'type' => '任务类型',
        'crontab' => '定时参数'
    ],
    'system_user' => [
        'id' => '系统用户ID',
        'username' => '用户名',
        'avatar' => '头像',
        'name' => '姓名',
        'desc' => '描述',
        'mobile' => '手机号',
        'email' => '邮箱',
        'rids' => '角色'
    ]
];