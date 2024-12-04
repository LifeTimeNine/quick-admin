<?php

namespace lang;

/**
 * 语言包常量
 */
class Variable
{
    // 异常Code
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
    const PARAM_ERROR = 'param error';
    /**
     * 操作失败
     */
    const ACTION_FAIL = 'action fail';
    /**
     * 数据不存在
     */
    const DATA_NOT_EXIST = 'data not exist';
    /**
     * Token 异常
     */
    const TOKEN_ERROR = 'token error';
    /**
     * Token 过期
     */
    const TOKEN_EXPIRE = 'token expire';
    /**
     * Token 刷新失败
     */
    const TOKEN_REFRESH_FAIL = 'token refresh fail';
    /**
     * Token 失效
     */
    const TOKEN_FAILURE = 'token failure';
    /**
     * 用户被禁用
     */
    const USER_DISABLE = 'user disable';
    /**
     * 用户被登录
     */
    const USER_LOGIN = 'user login';
    /**
     * 权限不足
     */
    const PERMISSION_DENIED = 'permission denied';

    // 普通的
    /**
     * 用户名或密码不正确
     */
    const USERNAME_OR_PASSWORD_NOT_CORRECT = 'username or password not correct';
    /**
     * 原密码不正确
     */
    const OLD_PASSWORD_NOT_CORRECT = 'old password not correct';
    /**
     * 两次输入的密码不一致
     */
    const ENTERED_PASSWORDS_DIFFER = 'entered passwords differ';

    // 上传
    /**
     * 文件不存在
     */
    const FILE_NOT_EXIST = 'file not exist';
    /**
     * 文件合并失败
     */
    const FILE_COMPLETE_FAil = 'file complete fail';
    /**
     * 不允许上传此类文件
     */
    const FILE_TYPE_NOT_ALLOWED = 'file type not allowed';

    // 验证器
    /**
     * 必填
     */
    const REQUIRED = 'require';
    /**
     * 超出最大字数限制
     */
    const MAXIMUM_WORD_LIMIT = 'maximum word limit';
    /**
     * 已存在
     */
    const HAS_EXIST = 'has exist';
    /**
     * 类型不合法
     */
    const TYPE_ILLEGAL = 'type illegal';
    /**
     * URL 不正确
     */
    const URL_NOT_CORRECT = 'url not correct';
    /**
     * 格式不正确
     */
    const FORMAT_CORRECT = 'format correct';
}