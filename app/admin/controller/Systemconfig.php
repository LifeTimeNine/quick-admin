<?php

namespace app\admin\controller;

use attribute\Action;
use attribute\Controller;
use model\SystemConfig as SystemConfigModel;
use traits\controller\QuickAction;


#[Controller('系统配置')]
class Systemconfig extends Basic
{

    use QuickAction;

    #[Action('系统配置列表', true, true)]
    public function list()
    {
        $this->returnList(SystemConfigModel::select()->toArray());
    }

    #[Action('编辑系统配置', true, log: true)]
    public function edit()
    {
        $this->_form(
            SystemConfigModel::class,
            null,
            ['value'],
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
