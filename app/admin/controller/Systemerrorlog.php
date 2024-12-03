<?php

namespace app\admin\controller;

use attribute\Action;
use attribute\Controller;
use model\SystemErrorLog as ModelSystemErrorLog;
use tools\Query;
use traits\controller\QuickAction;
use validate\SystemErrorLog as ValidateSystemErrorLog;

#[Controller('系统异常日志管理')]
class Systemerrorlog extends Basic
{
    use QuickAction;

    #[Action('系统异常日志列表', true, true)]
    public function list()
    {
        $query = new Query();
        $query->equal('hash,path_info,status');
        $this->_page(ModelSystemErrorLog::class, $query, $query->sortRule('id,happen_time,last_happen_time'), function(&$data) {
            $data->load([
                    'resolveUser' => function($query) {
                        $query->field(['id', 'username']);
                    }
                ]);
        });
    }

    #[Action('处理系统异常日志', true, log: true)]
    public function resolve()
    {
        $this->_form(
            ModelSystemErrorLog::class,
            ValidateSystemErrorLog::class . '.resolve',
            ['status','resolve_suid','resolve_time','resolve_remark'],
            null,
            function(&$data) {
                $data['status'] = 2;
                $data['resolve_suid'] = $this->getSuid();
                $data['resolve_time'] = date('Y-m-d H:i:s');
            }
        );
    }
}