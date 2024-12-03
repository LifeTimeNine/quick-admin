<?php

namespace response;

enum Code: int {

    /** 正常 */
    case SUCCESS = 0;
    /** 失败 */
    case ERROR = 10000;
    /** 参数异常 */
    case PARAM_ERROR = 10001;
    /** 操作失败 */
    case ACTION_FAIL = 10002;
    /** 数据不存在 */
    case DATA_NOT_EXIST = 10003;
    /** token异常 */
    case TOKEN_ERROR = 10101;
    /** token过期 */
    case TOKEN_EXPIRE = 10102;
    /** token刷新失败 */
    case TOKEN_REFRESH_FAIL = 10103;
    /** token 失效 */
    case TOKEN_FAILURE = 10104;
    /** 用户被禁用 */
    case USER_DISABLE = 10201;
    /** 用户被登录 */
    case USER_LOGIN = 10202;
    /** 权限不足 */
    case PERMISSION_DENIED = 10203;

    /**
     * 消息
     * @access  public
     * @return  string
     */
    public function message(): string
    {
        return match ($this) {
            Code::SUCCESS => 'success',
            Code::ERROR => 'error',
            Code::PARAM_ERROR => 'param error',
            Code::ACTION_FAIL => 'action fail',
            Code::DATA_NOT_EXIST => 'data not exist',
            Code::TOKEN_ERROR => 'token error',
            Code::TOKEN_EXPIRE => 'token expire',
            Code::TOKEN_REFRESH_FAIL => 'token refresh fail',
            Code::TOKEN_FAILURE => 'token_failure',
            Code::USER_DISABLE => 'user disable',
            Code::USER_LOGIN => 'user login',
            Code::PERMISSION_DENIED => 'permission denied'
        };
    }
}
