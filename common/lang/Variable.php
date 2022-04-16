<?php

namespace lang;

/**
 * 语言包常量
 */
class Variable
{
    // todo 异常Code 消息
    /**
     * 成功
     */
    const SUCCESS = 'success';
    /**
     * 错误
     */
    const ERROR = 'error';
    /**
     * 参数异常
     */
    const PARAM_ERROR = 'param_error';
    /**
     * 操作失败
     */
    const ACTION_FAIL = 'action_fail';
    /**
     * 数据不存在
     */
    const DATA_NOT_EXIST = 'data_not_exist';
    /**
     * Token 异常
     */
    const TOKEN_ERROR = 'token_error';
    /**
     * Token 过期
     */
    const TOKEN_EXPIRE = 'token_expire';
    /**
     * Token 刷新失败
     */
    const TOKEN_REFRESH_FAIL = 'token_refresh_fail';
    /**
     * Token 失效
     */
    const TOKEN_FIALURE = 'token_fialure';
    /**
     * 用户被禁用
     */
    const USER_DISABLE = 'user_disable';
    /**
     * 用户被登录
     */
    const USER_LOGIN = 'user_login';
    /**
     * 权限不足
     */
    const PERMISSION_DENIED = 'permtssion_denied';

    // todo 普通的
    /**
     * 用户名或密码不正确
     */
    const USERNAME_OR_PASSWORD_NOT_CORRECT = 'username_or_password_not_correct';
    /**
     * 原密码不正确
     */
    const OLD_PASSWORD_NOT_CORRECT = 'old_password_not_correct';
    /**
     * 两次输入的密码不一致
     */
    const ENTERRED_PASSWORDS_DIFFER = 'enterred_passwords_differ';

    // todo 上传
    /**
     * 文件不存在
     */
    const FILE_NOT_EXIST = 'file_not_exist';
    /**
     * 文件合并失败
     */
    const FILE_COMPLETE_FAil = 'file_complete_fail';
    /**
     * 不允许上传此类文件
     */
    const FILE_TYPE_NOT_ALLOWED = 'file_type_not_allowed';

    // todo 验证器
    /**
     * 必填
     */
    const REQUIRED = 'require';
    /**
     * 超出最大字数限制
     */
    const MAXIMUN_WORD_LIMIT = 'maximun_word_limit';
    /**
     * 已存在
     */
    const HAS_EXIST = 'has_exist';
    /**
     * 类型不合法
     */
    const TYPE_ILLEGAL = 'type_illegal';
    /**
     * URL 不正确
     */
    const URL_NOT_CORRECT = 'url_not_correct';
    /**
     * 格式不正确
     */
    const FORMAT_CORRECT = 'format_correct';
}