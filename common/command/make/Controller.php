<?php

namespace command\make;

use think\console\input\Option;
use think\helper\Str;

/**
 * 创建控制器
 */
class Controller extends Basic
{
    protected function configure()
    {
        parent::configure();
        $this->setName('make:controller')
            ->addOption('appName', 'a', Option::VALUE_OPTIONAL, 'App name', 'admin')
            ->setDescription('Create a new controller class');
    }

    /**
     * 获取命名空间
     * @access  protected
     * @return  string
     */
    protected function getNamespace(): string
    {
        $appName = $this->input->getOption('appName');
        $controller = $this->app->config->get('route.controller_layer');
        return "app\\{$appName}\\{$controller}";
    }

    /**
     * 获取变量
     * @access  protected
     * @param   string  $name   类名
     * @return  array
     */
    protected function getVariable(string $name): array
    {
        $appName = $this->input->getOption('appName');
        return [
            'app_name' => $appName,
            'model_name' => $name,
            'validate_name' => $name,
        ];
    }

    /**
     * 获取类名
     * @access  protected
     * @param   string  $name   类名
     * @return  string
     */
    protected function getClassName(string $name): string
    {
        $controllerSuffix = $this->app->config->get('route.controller_suffix');
        return Str::title(Str::lower($name)) . $controllerSuffix;
    }

    /**
     * 获取模板文件路径
     * @access protected
     * @return  string
     */
    protected function getTpl(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'tpls' . DIRECTORY_SEPARATOR . 'controller.tpl';
    }

    /**
     * 获取文件路径
     * @access  protected
     * @param   string  $name   类名
     * @return  string
     */
    protected function getPath(string $name): string
    {
        $appName = $this->input->getOption('appName');
        $controller = $this->app->config->get('route.controller_layer');
        return $this->app->getAppPath() . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $this->getClassName($name) . '.php';
    }
}