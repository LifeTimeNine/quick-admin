<?php

namespace app\admin\controller;

use model\SystemConfig as SystemConfigModel;
use service\SystemConfig as SystemConfigService;;
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
     * 编辑系统配置
     * @auth    true
     * @log     true
     */
    public function edit()
    {
        $this->_form(
            SystemConfigModel::class,
            SystemConfigValidate::class . '.edit',
            ['name', 'type', 'value'],
            null,
            null,
            function($model, $data) {
                SystemConfigModel::refreshCache($data['key']);
            }
        );
    }

    /**
     * 基础配置
     */
    public function basic()
    {
        $this->returnMap(SystemConfigModel::batchGet([
            SystemConfigModel::KEY_SYSTEM_NAME
        ]));
    }
}
