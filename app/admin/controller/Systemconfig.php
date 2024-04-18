<?php

namespace app\admin\controller;

use model\SystemConfig as SystemConfigModel;
use service\SystemConfig as SystemConfigService;
use tools\Query;
use traits\controller\QuickAction;
use validate\SystemConfig as SystemConfigValidate;

/**
 * 系统配置
 */
class Systemconfig extends Basic
{

    use QuickAction;

    /**
     * 系统配置列表
     * @menu    true
     * @auth    true
     */
    public function list()
    {
        $this->returnList(SystemConfigModel::select()->toArray());
    }
    /**
     * 添加系统配置
     */
    public function add()
    {
        $this->_form(
            SystemConfigModel::class,
            SystemConfigValidate::class . '.add',
            ['key', 'name', 'type', 'value'],
            null,
            null,
            function() {
                SystemConfigService::instance()->refresh();
            }
        );
    }
    /**
     * 编辑系统配置
     * @auth    true
     */
    public function edit()
    {
        $this->_form(
            SystemConfigModel::class,
            SystemConfigValidate::class . '.edit',
            ['name', 'type', 'value'],
            null,
            null,
            function() {
                SystemConfigService::instance()->refresh();
            }
        );
    }
    /**
     * 删除系统配置
     */
    public function delete()
    {
        $this->_delete(SystemConfigModel::class);
    }
    /**
     * 基础配置
     */
    public function basic()
    {
        $this->returnMap(SystemConfigService::instance()->batchGet(['system_name']));
    }
}
