<?php

namespace app\admin\controller;

use model\SystemTask as SystemTaskModel;
use model\SystemTaskLog;
use service\SystemTask as SystemTaskService;
use service\Code;
use tools\Query;
use traits\controller\QuickAction;
use validate\SystemTask as SystemTaskValidate;

/**
 * 系统任务管理
 */
class Systemtask extends Basic
{
    use QuickAction;
    /**
     * 获取服务状态
     */
    public function status()
    {
        $this->returnMap([
            'running' => SystemTaskService::instance()->isRunning(),
            'command' => 'sudo php ' . root_path() . 'think system:task -d'
        ]);
    }
    /**
     * 系统任务列表
     * @menu    true
     * @auth    true
     */
    public function list()
    {
        $query = new Query;
        $query->like('title')
            ->equal('exec_status,status');
        $this->_page(SystemTaskModel::class, $query->parse());
    }
    /**
     * 添加系统任务
     * @auth    true
     * @log     true
     */
    public function add()
    {
        $this->_form(
            SystemTaskModel::class,
            SystemTaskValidate::class . '.add',
            ['title', 'command','params','type','crontab'],
            null,
            null,
            function($model) {
                if ($model->type == SystemTaskModel::TYPE_TIMING && !SystemTaskService::instance()->set($model->id, $model->crontab)) {
                    return false;
                }
            }
        );
    }
    /**
     * 编辑系统任务
     * @auth    true
     * @log     true
     */
    public function edit()
    {
        $this->_form(
            SystemTaskModel::class,
            SystemTaskValidate::class,
            ['title', 'command','params','type','crontab'],
            null,
            null,
            function($model) {
                if ($model->type == SystemTaskModel::TYPE_TIMING && !SystemTaskService::instance()->set($model->id, $model->crontab)) {
                    return false;
                }
            }
        );
    }
    /**
     * 修改系统任务状态
     * @auth    true
     * @log     true
     */
    public function modifyStatus()
    {
        $this->_save(
            SystemTaskModel::class,
            [
            'status' => !empty($this->request->post('enable')) ? 1 : 2,
            ],
            null,
            null,
            function($model) {
                if($model->status == 1) {
                    if (!SystemTaskService::instance()->append($model->id)) {
                        return false;
                    }
                } else {
                    if (!SystemTaskService::instance()->remove($model->id)) {
                        return false;
                    }
                }
            }
        );
    }
    /**
     * 删除系统任务
     * @auth    true
     * @log     true
     */
    public function delete()
    {
        $this->_delete(
            SystemTaskModel::class,
            true,
            null,
            function($pk, $condition) {
                SystemTaskLog::whereIn('stid', $condition)->delete();
                if (!SystemTaskService::instance()->remove($condition)) {
                    return false;
                }
            }
        );
    }
    /**
     * 系统任务日志
     * @auth    true
     */
    public function logList()
    {
        $this->_page(SystemTaskLog::class, ['stid' => $this->request->get('stid/d')]);
    }
    /**
     * 执行系统任务
     * @auth    true
     * @log     true
     */
    public function exec()
    {
        if (!$this->request->has('id', 'post', true)) {
            $this->error(Code::PARAM_ERROR, '缺少必须参数 id');
        }
        if (SystemTaskService::instance()->exec($this->request->post('id/d'))) {
            $this->success();
        } else {
            $this->error(Code::ACTION_FAIL);
        }
    }
}