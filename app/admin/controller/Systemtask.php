<?php

namespace app\admin\controller;

use lang\Variable;
use model\SystemTask as SystemTaskModel;
use model\SystemTaskLog;
use service\Code;
use service\Timer;
use think\facade\Log;
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
        $status = Timer::instance()->state();
        $this->returnMap([
            'running' => $status !== false && $status['status'] == 0,
            'command' => 'systemctl start timer'
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
        $this->_page(SystemTaskModel::class, $query, $query->sortRule('id'));
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
            ['title', 'exec_file','args','type','cron'],
            null,
            null,
            function($model) {
                $result = Timer::instance()->taskSave(
                    $model->id,
                    $model->exec_file,
                    $model->args,
                    $model->type == SystemTaskModel::TYPE_TIMING,
                    1,
                    $model->cron
                );
                if ($result === false || $result['status'] <> 0) return false;
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
            ['title', 'exec_file','args','type','cron'],
            null,
            null,
            function($model) {
                $result = Timer::instance()->taskSave(
                    $model->id,
                    $model->exec_file,
                    $model->args,
                    $model->type == SystemTaskModel::TYPE_TIMING,
                    1,
                    $model->cron
                );
                if ($result === false || $result['status'] <> 0) return false;
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
                $result = Timer::instance()->taskStatus($model->id, $model->status == 1);
                Log::error([$model->id, $result, Timer::instance()->getError()]);
                if ($result === false || $result['status'] <> 0) {
                    return false;
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
                $result = Timer::instance()->taskRemove($condition);
                if ($result === false || $result['status'] <> 0) return false;
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
        $result = Timer::instance()->taskRun($this->request->post('id/d'));
        if ($result !== false && $result['status'] == 0) {
            $this->success();
        } else {
            $this->error(Code::ACTION_FAIL);
        }
    }

    /**
     * 通知
     */
    public function notify()
    {
        $event = $this->request->post('event');
        $data = $this->request->post('data');
        $task = SystemTaskModel::field('id')->find($data['uuid']);
        if (empty($task)) {
            $this->error(Code::DATA_NOT_EXIST);
        }
        Log::info(['event' => $event, 'data' => $data]);
        switch($event) {
            case 1: // 执行前通知
                SystemTaskModel::update([
                    'exec_status' => 2,
                    'next_exec_time' => $data['next_run_time'] ?: null
                ], ['id' => $data['uuid']]);
                break;
            case 2: // 执行结束
                SystemTaskModel::update([
                    'exec_status' => 1,
                    'last_exec_time' => $data['start_time'],
                    'last_exec_result' => $data['is_normal_exit'] ? 1 : 2,
                    'exec_num' => ['inc', 1],
                    'success_num' => ['inc', $data['is_normal_exit'] ? 1 : 0],
                    'fail_num' => ['inc', $data['is_normal_exit'] ? 0 : 1],
                ], ['id' => $data['uuid']]);
                SystemTaskLog::create([
                    'stid' => $data['uuid'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'runtime' => $data['runtime'],
                    'out' => $data['out'],
                    'err' => $data['err'],
                    'result' => $data['is_normal_exit'] ? 1 : 2,
                ]);
                break;
        }
        $this->success();
    }
}