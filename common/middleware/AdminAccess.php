<?php

namespace middleware;

use basic\AccessMiddleware;
use model\SystemActionLog;
use model\SystemUser;
use service\Node;
use service\Token;
use service\Code;

/**
 * admin 应用访问控制
 */
class AdminAccess extends AccessMiddleware
{
    protected $white = [
        'systemuser' => ['pwdLogin'],
        'systemconfig' => ['basic'],
        'upload' => ['file', 'part']
    ];

    /**
     * 当前应用信息
     * @var \think\App
     */
    protected $app;

    public function __construct(\think\App $app)
    {
        $this->app = $app;
    }

    /**
     * 逻辑处理
     * @access public
     * @param   \think\Request  $request
     * @param   \Closure    $next
     * @return  \think\Response
     */
    public function handle(\think\Request $request, \Closure $next)
    {
        // 如果是白名单，直接跳过
        if ($this->isWhite()) return $next($request);

        // 获取token
        $authorization = $request->header('authorization', '');
        if (empty($authorization) || strpos('Token ', $authorization) != 0) {
            return json(Code::buildMsg(Code::TOKEN_ERROR));
        }
        $tokenService = Token::instance();
        $uid = $tokenService->parse(substr($authorization, 6), 'login');
        if ($uid === false) {
            return json(Code::buildMsg($tokenService->getError()));
        }
        // 获取用户信息
        $userModel = SystemUser::find($uid);
        // 验证用户是否存在
        if (empty($userModel)) return json(Code::buildMsg(Code::TOKEN_ERROR));
        // 验证用户状态
        if ($userModel->status <> 1) return json(Code::buildMsg(Code::USER_DISABLE));
        // 验证最后登录时间
        if (date('Y-m-d H:i:s', $tokenService->getAll()['iat']) <> $userModel->last_login_time) {
            return json(Code::buildMsg(Code::USER_LOGIN));
        }
        $request->withMiddleware(['system_user_model' => $userModel]);
        // 验证用户权限
        if (!Node::instance()->check($uid)) return json(Code::buildMsg(Code::PERMISSION_DENIED));

        /**@var \think\Response */
        $response = $next($request);
        if (!empty($refreshToken = $tokenService->getRefreshToken())) {
            $response->header(['Refresh-Token' => $refreshToken]);
        }
        return $response;
    }

    public function end(\think\Response $response)
    {
        $nodeService = Node::instance();
        if ($nodeService->log()) {
            SystemActionLog::create([
                'suid' => $this->app->request->middleware('system_user_model')->id ?? null,
                'node' => $nodeService->getCurrentNode(),
                'request_time' => date('Y-m-d H:i:s', $this->app->request->time()),
                'request_param' => $this->app->request->param(),
                'request_ip'=> $this->app->request->ip(),
                'response_code' => $response->getCode(),
                'response_content'=> $response->getContent(),
                'run_time' => number_format(microtime(true) - $this->app->getBeginTime(), 6, '.', ''),
            ]);
        }
    }
}