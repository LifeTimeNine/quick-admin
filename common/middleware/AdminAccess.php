<?php

namespace middleware;

use basic\AccessMiddleware;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
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
        'systemconfig' => ['basic'],
        'systemuser' => ['pwdLogin'],
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
     * @retrun \think\Response
     */
    public function handle(\think\Request $request, \Closure $next)
    {
        // 如果是白名单，直接跳过
        if ($this->isWhite()) return $next($request);

        // 获取token
        $accessToken = $request->header('access-token');
        try {
            // 验证token
            $uid = Token::instance('admin')->parseUid($accessToken);
        } catch(SignatureInvalidException $e) { // token不正确
            return json(Code::buildMsg(Code::TOKEN_ERROR));
        } catch(ExpiredException $e) { // token过期
            return json(Code::buildMsg(Code::TOKEN_EXPIRE));
        } catch (\Throwable $th) { //其他异常
            return json(Code::buildMsg(Code::TOKEN_ERROR));
        }
        // 获取用户信息
        $userModel = SystemUser::find($uid);
        // 验证用户是否存在
        if (empty($userModel)) return json(Code::buildMsg(Code::TOKEN_ERROR));
        // 验证用户状态
        if ($userModel->status <> 1) return json(Code::buildMsg(Code::USER_DISABLE));
        // 验证最后登录时间
        if (date('Y-m-d H:i:s', Token::instance('admin')->getData()->iat) <> $userModel->last_login_time) {
            return json(Code::buildMsg(Code::USER_LOGIN));
        }
        $request->withMiddleware(['system_user_model' => $userModel]);
        // 验证用户权限
        if (!Node::instance()->check($uid)) return json(Code::buildMsg(Code::PERMISSION_DENIED));

        return $next($request);
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