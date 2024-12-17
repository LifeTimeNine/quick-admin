<?php

namespace app\admin\controller;

use attribute\Action;
use attribute\Controller;
use model\SystemTask as SystemTaskModel;
use model\SystemTaskLog;
use response\Code;
use service\Timer;
use think\facade\Log;
use tools\Query;
use traits\controller\QuickAction;
use validate\SystemTask as SystemTaskValidate;

#[Controller('系统任务管理')]
class Systemtask extends Basic
{
    use QuickAction;

    #[Action('获取服务状态', true)]
    public function status()
    {
        $status = Timer::instance()->state();
        $this->returnMap([
            'running' => $status !== false && $status['status'] == 0,
            'command' => 'systemctl start timer'
        ]);
    }

    #[Action('系统任务列表', true, true)]
    public function list()
    {
        $query = new Query;
        $query->like('title')
            ->equal('exec_status,status');
        $this->_page(SystemTaskModel::class, $query, $query->sortRule('id'));
    }

    #[Action('添加系统任务', true, log: true)]
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

    #[Action('编辑系统任务', true, log: true)]
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

    #[Action('修改系统任务状态', true, log: true)]
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

    #[Action('删除系统任务', true, log: true)]
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

    #[Action('系统任务日志', true)]
    public function logList()
    {
        $this->_page(SystemTaskLog::class, ['stid' => $this->request->get('stid/d')]);
    }

    #[Action('执行系统任务', true, log: true)]
    public function exec()
    {
        $data = $this->request->post();
        $this->validate($data, SystemTaskValidate::class, 'exec');
        $result = Timer::instance()->taskRun($data['id']);
        if ($result !== false && $result['status'] == 0) {
            $this->success();
        } else {
            $this->error(Code::ACTION_FAIL);
        }
    }

    #[Action('结果通知')]
    public function notify()
    {
        $event = $this->request->post('event');
        $data = $this->request->post('data');
        $task = SystemTaskModel::field('id')->find($data['uuid']);
        if (empty($task)) {
            $this->error(Code::DATA_NOT_EXIST);
        }
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