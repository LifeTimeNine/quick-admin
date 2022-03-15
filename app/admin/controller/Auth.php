<?php

namespace app\admin\controller;

use model\SystemUser;
use service\Code;
use service\Token;

/**
 * 身份验证
 */
class Auth extends Basic
{
    /**
     * 刷新Token
     */
    public function refresh()
    {
        $tokenService = Token::instance();
        $data = $tokenService->refresh(
            $this->request->post('refresh_token', ''),
            'login',
            function($data) {
                $user = SystemUser::find($data['data']);
                if (empty($user) || date('Y-m-d H:i:s', $data['iat']) <> $user->last_login_time) {
                    $this->error(Code::TOKEN_REFRESH_FAIL);
                }
                return true;
            }
        );
        $this->returnMap($data);
    }
    /**
     * 通过密码获取Token
     */
    public function pwd()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        if (empty($username) || empty($password)) {
            $this->error(Code::PARAM_ERROR, '用户名或密码不存在');
        }

        $user = SystemUser::where('username', $username)
            ->whereOr('mobile', $username)
            ->whereOr('email', $username)
            ->find();
        if (empty($user) || $user->password <> $password) {
            $this->error(Code::PARAM_ERROR, '用户名或密码不存在');
        }
        if ($user->status <> 1) {
            $this->error(Code::USER_DISABLE);
        }
        $user->save([
            'last_login_time' => date('Y-m-d H:i:s', $this->request->time()),
            'last_login_ip' => $this->request->ip(),
            'login_num' => ['inc', 1]
        ]);
        $this->returnMap(Token::instance()->build($user->id, 'login'));
    }
}