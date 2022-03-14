<?php

namespace app\admin\controller;

use model\SystemConfig as SystemConfigModel;
use service\SystemConfig as SystemConfigService;
use think\facade\Db;
use service\Code;
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
        $query = new Query();
        $query->equal('key')
            ->like('name');
        $this->_page(SystemConfigModel::class, [], 'key asc');
    }
    /**
     * 添加系统配置
     */
    public function add()
    {
        $data = $this->request->post();
        $this->validate($data, SystemConfigValidate::class, 'add');
        // 启动事务
        Db::startTrans();
        try {
            SystemConfigModel::create($data, ['key', 'name', 'type', 'value']);
            SystemConfigService::instance()->refresh();
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error(Code::ACTION_FAIL);
        }
        $this->success();
    }
    /**
     * 编辑系统配置
     */
    public function edit()
    {
        $data = $this->request->post();
        $this->validate($data, SystemConfigValidate::class);
        // 启动事务
        Db::startTrans();
        try {
            SystemConfigModel::update($data, ['key' => $data['key']], ['name', 'type', 'value']);
            SystemConfigService::instance()->refresh();
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error(Code::ACTION_FAIL);
        }
        $this->success();
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