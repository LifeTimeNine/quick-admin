<?php

namespace basic;

/**
 * 访问控制中间件基类
 */
abstract class AccessMiddleware
{

    /**
     * 多应用 (非多应用不验证应用名称)
     * @var bool
     */
    protected $multiple = false;

    /**
     * 白名单
     * @var array
     */
    protected $white = [];

    /**
     * 检查是否在白名单中
     * @access protected
     * @return bool
     */
    protected function isWhite()
    {
        $request = request();
        $controller = $request->controller(true);
        $action = $request->action();
        if ($this->multiple) {
            $appName = app('http')->getName();
            return array_key_exists($appName, $this->white) &&
                array_key_exists($controller, $this->white[$appName]) &&
                in_array($action, $this->white[$appName][$controller]);
        } else {
            return array_key_exists($controller, $this->white) &&
                in_array($action, $this->white[$controller]);
        }
    }

    /**
     * 逻辑处理
     * @access public
     * @param   \think\Request  $request
     * @param   \Closure    $next
     * @return \think\Response
     */
    abstract public function handle(\think\Request $request, \Closure $next);
}