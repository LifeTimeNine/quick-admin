<?php

namespace service;

use lang\Variable;
use think\facade\Lang;

/**
 * 异常码集合
 */
class Code
{
    /**
     * 正常
     */
    const SUCCESS = [0, Variable::SUCCESS];
    /**
     * 失败
     */
    const ERROR = [10000, Variable::ERROR];
    /**
     * 参数异常
     */
    const PARAM_ERROR = [10001, Variable::PARAM_ERROR];
    /**
     * 操作失败
     */
    const ACTION_FAIL = [10002, Variable::ACTION_FAIL];
    /**
     * 数据不存在
     */
    const DATA_NOT_EXIST = [10003, Variable::DATA_NOT_EXIST];
    /**
     * token异常
     */
    const TOKEN_ERROR = [10101, Variable::TOKEN_ERROR];
    /**
     * token 过期
     */
    const TOKEN_EXPIRE = [10102, Variable::TOKEN_EXPIRE];
    /**
     * token 刷新失败
     */
    const TOKEN_REFRESH_FAIL = [10103, Variable::TOKEN_REFRESH_FAIL];
    /**
     * token 失效
     */
    const TOKEN_FAILURE = [10104, Variable::TOKEN_FAILURE];
    /**
     * 用户被禁用
     */
    const USER_DISABLE = [10201, Variable::USER_DISABLE];
    /**
     * 用户被登录
     */
    const USER_LOGIN = [10202, Variable::USER_LOGIN];
    /**
     * 权限不足
     */
    const PERMISSION_DENIED = [10203, Variable::PERMISSION_DENIED];

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
                'message' => Lang::get($message ?: ($code[1] ?? self::ERROR[1])),
                'data' => $data,
            ];
        } else {
            return [
                'code' => $code,
                'message' => Lang::get($message),
                'data' => $data,
            ];
        }
    }
}