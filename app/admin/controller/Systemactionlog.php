<?php

namespace app\admin\controller;

use attribute\Action;
use attribute\Controller;
use model\SystemActionLog as SystemActionLogModel;
use model\SystemUser;
use service\Node;
use tools\Query;
use traits\controller\QuickAction;

#[Controller('系统操作日志管理')]
class Systemactionlog extends Basic
{
    use QuickAction;

    #[Action('系统操作日志列表', true, true)]
    public function list()
    {
        $query = new Query();
        $query->equal('node')
            ->parseParam('username', function($key, $value, $query) {
                $query->append('suid', 'in', function($query) use($key, $value){
                    $query->table(SystemUser::getTableName())
                        ->whereLike($key, "%{$value}%")
                        ->field('id');
                });
            });
        $this->_page(SystemActionLogModel::class, $query, $query->sortRule('id'), function(&$items) {
            $items->load(['systemUser'])
                ->visible(['systemUser' => ['username', 'name', 'avatar']])
                ->withAttr([
                    'node_title' => function($value, $data) {
                        return Node::instance()->getNodeInfo($data['node'])['title'] ?? '';
                    }
                ])
                ->append(['node_title']);
        });
    }
}