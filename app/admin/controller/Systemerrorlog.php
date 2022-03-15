<?php

namespace app\admin\controller;

use model\SystemErrorLog as ModelSystemErrorLog;
use tools\Query;
use traits\controller\QuickAction;

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
        $this->_page(ModelSystemErrorLog::class, $query->parse(), 'id desc', function(&$data) {
            $data->load(['resolveUser'])
                ->visible(['resolveUser' => ['username']]);
        });
    }

    /**
     * 处理系统异常日志
     * @auth    true
     * @log     true
     */
    public function resolve()
    {
        $this->_save(ModelSystemErrorLog::class, [
            'status' => 2,
            'resolve_suid' => $this->getSuid(),
            'resolve_time' => date('Y-m-d H:i:s')
        ], [
            'status' => 1
        ]);
    }
}