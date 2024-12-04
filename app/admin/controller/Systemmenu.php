<?php

namespace app\admin\controller;

use attribute\Action;
use attribute\Controller;
use model\SystemMenu as SystemMenuModel;
use service\Node;
use tools\Tools;
use traits\controller\QuickAction;
use validate\SystemMenu as SystemMenuValidate;

#[Controller('系统菜单管理')]
class Systemmenu extends Basic
{

    use QuickAction;

    #[Action('系统菜单列表', true, true)]
    public function list()
    {
        $list = SystemMenuModel::order('sort', 'desc')->select()->hidden(['delete_time'])->toArray();
        $list = Tools::instance()->arr2tree($list, 0, function($item) {
            if (count($item['children']) == 0) {
                unset($item['children']);
            }
            return $item;
        });
        $this->returnList($list);
    }

    #[Action('系统菜单回收站列表', true, true)]
    public function recycleList()
    {
        $this->_page(
            SystemMenuModel::onlyTrashed(),
            [],
            'delete_time desc',
        );
    }

    #[Action('添加系统菜单', true, log: true)]
    public function add()
    {
        $this->_form(
            SystemMenuModel::class,
            SystemMenuValidate::class . '.add',
            ['pid','title','icon','url','node','params']
        );
    }

    #[Action('编辑系统菜单', true, log: true)]
    public function edit()
    {
        $this->_form(
            SystemMenuModel::class,
            SystemMenuValidate::class . '.edit',
            ['pid','title','icon','url','node','params']
        );
    }

    #[Action('修改系统菜单状态', true, log: true)]
    public function modifyStatus()
    {
        $this->_save(SystemMenuModel::class, [
            'status' => !empty($this->request->post('enable')) ? 1 : 2,
        ]);
    }

    #[Action('软删除系统菜单', true, log: true)]
    public function softDelete()
    {
        $this->_delete(SystemMenuModel::class);
    }

    #[Action('恢复软删除系统菜单', true, log: true)]
    public function restore()
    {
        $this->_restore(SystemMenuModel::class);
    }

    #[Action('完全删除系统菜单', true, log: true)]
    public function delete()
    {
        $this->_delete(SystemMenuModel::class, true);
    }

    #[Action('设置系统菜单排序权重', true, log: true)]
    public function setSort()
    {
        $this->_save(SystemMenuModel::class, [
            'sort' => $this->request->post('sort/d', 0),
        ]);
    }

    /**
     * 获取菜单节点列表
     */
    public function getUserMenuNodes()
    {
        $this->returnList(Node::instance()->getUserMenuNodes($this->getSuid()));
    }
}