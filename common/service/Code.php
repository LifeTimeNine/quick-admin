<?php

namespace service;

/**
 * 异常码集合
 */
class Code
{
    /**
     * 正常
     */
    const SUCCESS = [0, 'SUCCESS'];
    /**
     * 失败
     */
    const ERROR = [10000, 'ERROR'];
    /**
     * 参数异常
     */
    const PARAM_ERROR = [10001, '参数异常'];
    /**
     * 操作失败
     */
    const ACTION_FAIL = [10002, '操作失败'];
    /**
     * 数据不存在
     */
    const DATA_NOT_EXIST = [10003, '数据不存在'];
    /**
     * token异常
     */
    const TOKEN_ERROR = [10101, '身份信息验证失败请重新登录'];
    /**
     * token 过期
     */
    const TOKEN_EXPIRE = [10102, '身份信息已过期请重新登录'];
    /**
     * token 刷新失败
     */
    const TOKEN_REFRESH_FAIL = [10103, '身份信息刷新失败'];
    /**
     * token 失效
     */
    const TOKEN_FIALURE = [10104, '身份信息已失效请重新登录'];
    /**
     * 用户被禁用
     */
    const USER_DISABLE = [10201, '账户已被禁用，请联系管理员'];
    /**
     * 用户被登录
     */
    const USER_LOGIN = [10202, '该账户已在其他地点被登录'];
    /**
     * 权限不足
     */
    const PERMISSION_DENIED = [10203, '权限不足，无法访问此功能'];

    /**
     * 构建返回数据
     * @access  public
     * @param   int|array   $code       异常码(自定义或从 Code 类中取)
     * @param   string      $message    消息
     * @param   array       $data       数据
     * @return  array
     */
    public static function buildMsg($code, string $message = null, $data = null)
    {
        if (is_array($code)) {
            return [
                'code' => $code[0],
                'message' => $message ?: ($code[1] ?? self::ERROR[1]),
                'data' => $data,
            ];
        } else {
            return [
                'code' => $code,
                'message' => $message,
                'data' => $data,
            ];
        }
    }
}