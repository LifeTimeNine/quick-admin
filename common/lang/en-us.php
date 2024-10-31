<?php

use lang\Variable;

return [
    /** 异常Code 消息 */
    Variable::SUCCESS => 'Success',
    Variable::ERROR => 'Error',
    Variable::PARAM_ERROR => 'Parameter abnormal',
    Variable::ACTION_FAIL => 'Operation failure',
    Variable::DATA_NOT_EXIST => 'Data does not exist',
    Variable::TOKEN_ERROR => 'Authentication failed. Please log in again',
    Variable::TOKEN_EXPIRE => 'The identity information has expired. Please log in again',
    Variable::TOKEN_REFRESH_FAIL => 'Description Failed to refresh the identity information',
    Variable::TOKEN_FAILURE => 'The identity information is invalid. Please log in again',
    Variable::USER_DISABLE => 'Account has been disabled',
    Variable::USER_LOGIN => 'Account has been logged in from another location',
    Variable::PERMISSION_DENIED => 'Insufficient permissions to access this feature',

    Variable::USERNAME_OR_PASSWORD_NOT_CORRECT => 'User name or password is incorrect',
    Variable::OLD_PASSWORD_NOT_CORRECT => 'The original password is incorrect',
    Variable::ENTERED_PASSWORDS_DIFFER => 'The entered passwords are inconsistent',

    Variable::FILE_NOT_EXIST => 'File not exist',
    Variable::FILE_COMPLETE_FAil => 'File merge failed',
    Variable::FILE_TYPE_NOT_ALLOWED => 'Uploading such files is not allowed',

    Variable::REQUIRED => 'Please enter the:attribute',
    Variable::MAXIMUM_WORD_LIMIT => ':attribute exceeds the maximum word limit',
    Variable::HAS_EXIST => 'The :attribute exists',
    Variable::TYPE_ILLEGAL => 'The :attribute type is invalid',
    Variable::URL_NOT_CORRECT => 'The :attribute address is incorrect',
    Variable::FORMAT_CORRECT => 'The :attribute format is incorrect',

    /** 数据表字段 */
    'system_config' => [
        'id' => 'System Configuration ID',
        'key' => 'Configuration key',
        'name' => 'Configuration name'
    ],
    'system_role' => [
        'id' => 'system role ID',
        'name' => 'system role name',
        'desc' => 'system role desc',
    ],
    'system_menu' => [
        'id' => 'Menu ID',
        'pid' => 'Parent menu',
        'title' => 'Title',
        'icon' => 'Icon',
        'url' => 'Page url',
        'node' => 'Permissions nodes',
        'params' => 'Parameter'
    ],
    'system_task' => [
        'id' => 'System Task ID',
        'title' => 'System Task name',
        'exec_file' => 'Task command',
        'args' => 'Task Parameter',
        'type' => 'Task type',
        'cron' => 'Timing parameters'
    ],
    'system_user' => [
        'id' => 'System User ID',
        'username' => 'User name',
        'avatar' => 'Avatar',
        'name' => 'Name',
        'desc' => 'Description',
        'mobile' => 'Mobile phone no.',
        'email' => 'Email',
        'rids' => 'Role'
    ],
    'system_error_log' => [
        'resolve_remark' => 'Remark'
    ]
];