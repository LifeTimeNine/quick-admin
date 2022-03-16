<?php

namespace app\admin\controller;

use model\SystemMenu;
use model\SystemUser as SystemUserModel;
use model\SystemUserRole;
use service\Node;
use service\Code;
use tools\Query;
use tools\Tools;
use traits\controller\QuickAction;
use validate\SystemUser as SystemUserValidate;

/**
 * 用户管理
 */
class Systemuser extends Basic
{
    use QuickAction;
    /**
     * 获取用户信息
     */
    public function userInfo()
    {
        $this->returnMap($this->getSystemUserModel()->visible(['username','avatar','name','mobile','email'])->toArray());
    }
    /**
     * 编辑用户信息
     */
    public function editUserInfo()
    {
        $this->_form(
            SystemUserModel::class,
            SystemUserValidate::class . '.userEdit',
            ['avatar'],
            'id',
            function(&$data) {
                $data['id'] = $this->getSuid();
            }
        );
    }
    /**
     * 获取用户菜单
     */
    public function getMenu()
    {
        $node = Node::instance();
        $nodes = array_merge($node->getUserNodes($this->getSuid()), $node->getPublicNodes());

        $menus = SystemMenu::enable()
            ->order('sort', 'desc')
            ->field(['id','pid','title','icon', 'url','node','params'])
            ->select()
            ->toArray();
        $menus = Tools::instance()->arr2tree($menus, 0, function($item) use($nodes){
            if (
                ($item['url'] == '#' && count($item['children']) == 0) ||
                (!empty($item['node']) && !in_array($item['node'], $nodes))
            ) {
                return false;
            } else {
                return $item;
            }
        });
        
        $this->returnMap([
            'nodes' => $nodes,
            'menus' => $menus
        ]);
    }

    /**
     * 修改密码
     */
    public function modifyPwd()
    {
        if ($this->request->post('old_password') <> $this->getSystemUserModel()->password) {
            $this->error(Code::PARAM_ERROR, '原密码不正确');
        }
        if ($this->request->post('new_password') <> $this->request->post('confirm_password')) {
            $this->error(Code::PARAM_ERROR, '两次输入的密码不一致');
        }
        $this->getSystemUserModel()->save(['password' => $this->request->post('new_password')]);
        $this->success();
    }

    /**
     * 系统用户列表
     * @menu    true
     * @auth    true
     */
    public function list()
    {
        $query = new Query();
        $query->like('username,name')
            ->equal('status')
            ->append('id', '<>', 1);
        $model = SystemUserModel::with([
            'roles'
        ])
            ->where($query->parse())
            ->visible([
                'id','username','avatar','name','desc','create_time',
                'status','last_login_time','last_login_ip','login_num',
                'roles'=>['id', 'name']
            ]);
        $this->_page($model);
    }
    /**
     * 创建系统用户
     * @auth    true
     * @log     true
     */
    public function add()
    {
        $this->_form(
            SystemUserModel::class,
            SystemUserValidate::class . '.add',
            ['username','password','name','desc'],
            null,
            function(&$data) {
                $data['password'] = md5($data['username']);
            },
            function($model, $data) {
                $model->roles()->attach($data['rids']);
            });
    }

    /**
     * 编辑系统用户
     * @auth    true
     * @log     true
     */
    public function edit()
    {
        $this->_form(
            SystemUserModel::class,
            SystemUserValidate::class . '.edit',
            ['name','desc'],
            null,
            null,
            function($model, $data) {
                SystemUserRole::where('suid', $data['id'])->delete();
                $model->roles()->attach($data['rids']);
            });
    }
    /**
     * 修改系统用户状态
     * @auth    true
     * @log     true
     */
    public function modifyStatus()
    {
        $this->_save(SystemUserModel::class, [
            'status' => !empty($this->request->post('enable')) ? 1 : 2,
        ]);
    }
    /**
     * 软删除系统用户
     * @auth    true
     * @log     true
     */
    public function softDelete()
    {
        $this->_delete(SystemUserModel::class);
    }
    /**
     * 恢复软删除系统用户
     * @auth    true
     * @log     true
     */
    public function restore()
    {
        $this->_restore(SystemUserModel::class);
    }
    /**
     * 完全删除系统用户
     * @auth    true
     * @log     true
     */
    public function delete()
    {
        $this->_delete(SystemUserModel::class, true);
    }
    /**
     * 重置密码
     * @auth    true
     * @log     true
     */
    public function resetPwd()
    {
        $user = SystemUserModel::find($this->request->post('id'));
        if (empty($user)) $this->error(Code::DATA_NOT_EXIST);

        $user->save([
            'password' => md5($user->username),
        ]);
        $this->success();
    }
    /**
     * 刷新信息
     */
    public function refresh()
    {
        Node::instance()->getUserNodes($this->getSuid(), true);
        $this->success();
    }
}