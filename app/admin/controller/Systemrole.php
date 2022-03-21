<?php

namespace app\admin\controller;

use model\SystemRole as SystemRoleModel;
use model\SystemRoleNode;
use service\Node;
use think\facade\Db;
use service\Code;
use tools\Query;
use traits\controller\QuickAction;
use validate\SystemRole as SystemRoleValid;

/**
 * 系统角色管理
 */
class Systemrole extends Basic
{
    use QuickAction;

    /**
     * 系统角色列表
     * @menu    true
     * @auth    true
     */
    public function list()
    {
        $query = new Query();
        $query->append('id', '<>', 1)
            ->append('create_suid', '=', $this->getSuid())
            ->like('name')
            ->equal('status');
        $this->_page(
            SystemRoleModel::class,
            $query->parse(),
            null,
            function($data) {
                $data->hidden(['create_suid', 'delete_time']);
            }
        );
    }
    /**
     * 系统角色回收站列表
     * @menu    true
     * @auth    true
     */
    public function recycleList()
    {
        $query = new Query();
        $query->append('id', '<>', 1)
            ->append('create_suid', '=', $this->getSuid())
            ->like('name')
            ->equal('status');
        $this->_page(
            SystemRoleModel::onlyTrashed(),
            $query->parse(),
            'delete_time desc',
            function($data) {
                $data->hidden(['create_suid', 'delete_time']);
            }
        );
    }
    /**
     * 添加系统角色
     * @auth    true
     * @log     true
     */
    public function add()
    {
        $this->_form(
            SystemRoleModel::class,
            SystemRoleValid::class . '.add',
            ['name', 'desc', 'create_suid'],
            null,
            function(&$data) {
                $data['create_suid'] = $this->getSuid();
            }
        );
    }
    /**
     * 系统角色详情
     * @auth    true
     */
    public function detail()
    {
        $model = SystemRoleModel::where('create_suid', $this->getSuid());
        $this->_detail($model, function(&$data) {
            $data->hidden(['create_suid', 'delete_time']);
        });
    }
    /**
     * 编辑系统角色
     * @auth    true
     * @log     true
     */
    public function edit()
    {
        $this->_form(
            SystemRoleModel::class,
            SystemRoleValid::class . '.edit',
            ['name', 'desc'],
        );
    }
    /**
     * 修改系统角色状态
     * @auth    true
     * @log     true
     */
    public function modifyStatus()
    {
        $this->_save(SystemRoleModel::class, [
            'status' => !empty($this->request->post('enable')) ? 1 : 2,
        ]);
    }
    /**
     * 软删除系统角色
     * @auth    true
     * @log     true
     */
    public function softDelete()
    {
        $this->_delete(SystemRoleModel::class);
    }
    /**
     * 系统角色软删除恢复
     * @auth    true
     * @log     true
     */
    public function restore()
    {
        $this->_restore(SystemRoleModel::class);
    }
    /**
     * 系统角色真实删除
     * @auth    true
     * @log     true
     */
    public function delete()
    {
        $this->_delete(SystemRoleModel::class, true, null, function($pk, $condition) {
            SystemRoleNode::whereIn('srid', $condition)->delete();
        });
    }

    /**
     * 获取用户节点树
     */
    public function getUserNodeTree()
    {
        $this->returnList(Node::instance()->getUserActionNodeTree($this->getSuid()));
    }

    /**
     * 获取角色节点
     */
    public function getRoleNodes()
    {
        $list = SystemRoleNode::where('srid', $this->request->get('srid'))
            ->column('node');
        $this->returnList($list); 
    }

    /**
     * 修改角色节点
     * @auth    true
     * @log     true
     */
    public function modifyRoleNodes()
    {
        if (empty(SystemRoleModel::find($this->request->post('srid')))) {
            $this->error(Code::DATA_NOT_EXIST);
        }
        // 启动事务
        Db::startTrans();
        try {
            SystemRoleNode::where('srid', $this->request->post('srid'))->delete();
            $ndoes = $this->request->post('nodes');
            if (is_array($ndoes) && count($ndoes) > 0) {
                $nodesData = [];
                foreach($ndoes as $item) {
                    $nodesData[] = [
                        'srid' => $this->request->post('srid'),
                        'node' => $item
                    ];
                }
                SystemRoleNode::insertAll($nodesData);
            }
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
     * 获取用户角色
     */
    public function getUserRole()
    {
        $list = SystemRoleModel::enable()
            ->where('id', '<>', 1)
            ->where('create_suid', $this->getSuid())
            ->field('id,name')
            ->select()
            ->toArray();
        $this->returnList($list);
    }
}