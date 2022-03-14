<?php

namespace app\admin\controller;

use basic\Controller;
use middleware\AdminAccess;
use middleware\Cors;

/**
 * 控制器基类
 */
abstract class Basic extends Controller
{
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        AdminAccess::class,
    ];

    /**
     * 获取当前访问系统用户模型
     * @access  protected
     * @return  \model\SystemUser
     */
    protected function getSystemUserModel()
    {
        return $this->request->middleware('system_user_model');
    }

    /**
     * 获取当前访问系统用户ID
     * @access  protected
     * @return  int
     */
    protected function getSuid()
    {
        return $this->getSystemUserModel()->id ?? null;
    }
}