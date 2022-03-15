<?php

namespace service;

use model\SystemRoleNode;
use model\SystemUserRole;
use traits\tools\Instance;

/**
 * 节系统点相关业务
 */
class Node
{
    use Instance;

    /**
     * 当前应用信息
     * @var \think\App
     */
    protected $app;
    /**
     * 当前请求信息
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用名称
     * @var string
     */
    protected $appName = 'admin';
    /**
     * 忽略的控制器
     * @var array
     */
    protected $ignoreControllers = ['basic'];
    /**
     * 忽略的操作
     * @var array
     */
    protected $ignoreActions = ['__construct'];

    /**
     * 应用节点缓树存前缀
     * @var string
     */
    protected $appNodeTreeCachePrefix = 'system_node_tree_';
    /**
     * 用户节点缓存标识
     * @var array
     */
    protected $userNodeCachePrefix = 'user_node_';

    /**
     * 应用节点树
     * @var array
     */
    protected $appNodeTree;
    /**
     * 当前节点
     * @var string
     */
    protected $currentNode;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->app = app();
        $this->request = request();
    }

    /**
     * 获取当前节点
     * @access  public
     * @return  string
     */
    public function getCurrentNode()
    {
        if (empty($this->currentNode)) {
            $this->currentNode = "{$this->request->controller(true)}/{$this->request->action()}";
        }
        return $this->currentNode;
    }

    /**
     * 获取节点信息
     * @access  public
     * @param   string  $node   节点名称，默认当前节点
     * @param   bool    $forceRefresh   强制刷新
     * @return  array
     */
    public function getNodeInfo($node = null, $forceRefresh = false)
    {
        if (empty($node)) {
            $node = $this->getCurrentNode();
        }
        [$controller, $action] = explode('/', $node);

        return $this->appNodeTree($forceRefresh)[$controller]['actions'][$action] ?? [];
    }

    /**
     * 验证当前节点是否可以访问
     * @access  public
     * @param   int     $uid
     * @return  bool
     */
    public function check($uid)
    {
        $publicNodes = $this->getPublicNodes($this->app->isDebug());
        $userNodes = $this->getUserNodes($uid, $this->app->isDebug());
        $currentNode = $this->getCurrentNode();
        return in_array($currentNode, $userNodes) || in_array($currentNode, $publicNodes);
    }

    /**
     * 获取用户可访问节点
     * @access  public
     * @param   int     $uid    用户ID
     * @param   bool    $forceRefresh   强制刷新
     * @return  array
     */
    public function getUserNodes($uid, $forceRefresh = false)
    {
        $cacheKey = "{$this->userNodeCachePrefix}{$uid}";
        if ($this->app->cache->has($cacheKey) && !$forceRefresh) {
            return $this->app->cache->get($cacheKey);
        }
        $srids = SystemUserRole::where('suid', $uid)->column('srid');
        if ($uid == 1 || in_array(1, $srids)) {
            $nodes = [];
            foreach($this->appNodeTree(true) as $controller => $controllerInfo) {
                foreach($controllerInfo['actions']??[] as $action => $actionInfo) {
                    if (!empty($actionInfo['auth'])) {
                        $nodes[] = "{$controller}/{$action}";
                    }
                }
            }
        } else {
            $nodes = SystemRoleNode::whereIn('srid', $srids)
                ->distinct(true)
                ->column('node');
        }
        $this->app->cache->set($cacheKey, $nodes);
        return $nodes;
    }

    /**
     * 获取应用节点树
     * @access  public
     * @param   bool    $forceRefresh   强制刷新
     * @return  array
     */
    public function appNodeTree($forceRefresh = false)
    {
        if (!empty($this->appNodeTree)) {
            return $this->appNodeTree;
        }
        $cacheKey = "{$this->appNodeTreeCachePrefix}_{$this->appName}";
        if ($this->app->cache->has($cacheKey) && !$forceRefresh) {
            return $this->app->cache->get($cacheKey);
        }
        $controllerDirName = $this->app->config->get('route.controller_layer', 'controller');
        $controllerPath = $this->app->getBasePath() . $this->appName . DIRECTORY_SEPARATOR . $controllerDirName . DIRECTORY_SEPARATOR;
        $controllerSuffix = $this->app->config->get('route.controller_suffix', true);
        $appNamespace = explode('\\', $this->app->getNamespace())[0];
        $data = [];
        foreach($this->scanDir($controllerPath, 2) as $controllerFileName) {
            $conntrollerName = pathinfo($controllerFileName, PATHINFO_FILENAME);
            $controller = strtolower($controllerSuffix ? str_replace('Controller', '', $conntrollerName) : $conntrollerName);
            if (in_array($controller, $this->ignoreControllers)) continue;
            $class = new \ReflectionClass("{$appNamespace}\\{$this->appName}\\{$controllerDirName}\\{$conntrollerName}");
            $data[$controller] = $this->parseAnnotation($class->getDocComment(), $controller);

            foreach($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $action) {
                if (in_array($action->getName(), $this->ignoreActions)) continue;
                $data[$controller]['actions'][$action->getName()] = $this->parseAnnotation($action->getDocComment(), $action->getName(), 2);
            }
        }
        $this->appNodeTree = $data;
        $this->app->cache->set($cacheKey, $data);
        return $data;
    }

    /**
     * 获取用户菜单节点
     * @access  public
     * @param   int     $uid            用户ID
     * @param   bool    $forceRefresh   强制刷新
     * @return  array
     */
    public function getUserMenuNodes($uid, $forceRefresh = false)
    {
        $userNodes = $this->getUserNodes($uid, $forceRefresh);
        $appNodeTree = $this->appNodeTree($forceRefresh);
        $data = [];
        foreach($appNodeTree as $controller => $controllerInfo) {
            foreach($controllerInfo['actions']??[] as $action => $actionInfo) {
                if (!empty($actionInfo['menu']) && in_array("{$controller}/{$action}", $userNodes)) {
                    $data[] = [
                        'node' => "{$controller}/{$action}",
                        'title' => $actionInfo['title']
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * 获取用户操作节点树
     * @access  public
     * @param   int     $uid            用户ID
     * @param   bool    $forceRefresh   强制刷新
     * @return  array
     */
    public function getUserActionNodeTree($uid, $forceRefresh = false)
    {
        $userNodes = $this->getUserNodes($uid, $forceRefresh);
        $appNodeTree = $this->appNodeTree($forceRefresh);
        $data = [];
        foreach($appNodeTree as $controller => $controllerInfo) {
            $actions = [];
            foreach($controllerInfo['actions']??[] as $action => $actionInfo) {
                if (!empty($actionInfo['auth']) && in_array("{$controller}/{$action}", $userNodes)) {
                    $actions[] = [
                        'node' => "{$controller}/{$action}",
                        'title' => $actionInfo['title']
                    ];
                }
            }
            if (!empty($actions)) {
                $data[] = [
                    'title' => $controllerInfo['title'],
                    'actions' => $actions,
                ];
            }
        }
        return $data;
    }

    /**
     * 获取公共节点
     * @access  public
     * @param   bool    $forceRefresh   强制刷新
     * @return  array
     */
    public function getPublicNodes($forceRefresh = false)
    {
        $nodes = [];
        foreach($this->appNodeTree($forceRefresh) as $controller => $controllerInfo) {
            foreach($controllerInfo['actions']??[] as $action => $actionInfo) {
                if (empty($actionInfo['auth'])) {
                    $nodes[] = "{$controller}/{$action}";
                }
            }
        }
        return $nodes;
    }

    /**
     * 获取当前节点是否需要写入日志
     * @access  public
     * @return  bool
     */
    public function log()
    {
        $appNodeTree = $this->appNodeTree($this->app->env->get('app_debug'));
        return !empty($appNodeTree[$this->request->controller(true)]['actions'][$this->request->action()]['log']);
    }

    /**
     * 扫描文件夹
     * @access  private
     * @param   string  $path   目录地址
     * @param   int     $type   类型 0-所有 1-文件夹 2-文件
     * @param   string  $ext    文件后缀
     * @return  array
     */
    private function scanDir($path, $type = 0, $ext = 'php')
    {
        if (!is_dir($path)) return [];
        $list = [];
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') continue;
            if ($type == 0 || ($type == 1 && is_dir("{$path}/{$item}"))) {
                $list[] = $item;
                continue;
            }
            $pathinfo = pathinfo("{$path}/{$item}");
            if ($type == 2 && is_file("{$path}/{$item}") && $pathinfo['extension'] == $ext) {
                $list[] = $pathinfo['filename'];
            }
        }
        return $list;
    }

    /**
     * 解析注释
     * @param   string  $annotation     注释内容
     * @param   string  $default        默认标题
     * @param   int     $type           类型 1-类注释 2-方法注释
     * @return  array
     */
    protected function parseAnnotation($annotation, $default = '', $type = 1)
    {
        $text = strtr($annotation, "\n", ' ');
        $title = preg_replace('/^\/\*\s*\*\s*\*\s*(.*?)\s*\*.*?$/', '$1', $text);
        $data = ['title' => $title ? $title : $default];
        if ($type == 2) {
            // 是否验证节点
            if (preg_match('/@auth\s*true/i', $text)) $data['auth'] = true;
            // 是否在菜单中显示
            if (preg_match('/@menu\s*true/i', $text)) $data['menu'] = true;
            //是否记录日志
            if (preg_match('/@log\s*true/i', $text)) $data['log'] = true;
        }
        return $data;
    }
}