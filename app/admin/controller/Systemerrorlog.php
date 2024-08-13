<?php

namespace app\admin\controller;

use model\SystemErrorLog as ModelSystemErrorLog;
use tools\Query;
use traits\controller\QuickAction;
use validate\SystemErrorLog as ValidateSystemErrorLog;

/**
 * 系统异常日志管理
 */
class Systemerrorlog extends Basic
{
    use QuickAction;

    /**
     * 系统异常日志列表
     * @menu    true
     * @auth    true
     */
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

    /**
     * 处理系统异常日志
     * @auth    true
     * @log     true
     */
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