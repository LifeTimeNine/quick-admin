<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemMenu extends Migrator
{
    public function change()
    {
        $table = $this->table('system_menu', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统菜单表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(Column::integer('id')->setLimit(10)->setSigned(false)->setIdentity(true)->setNull(false)->setComment('id'))
            ->addColumn(Column::integer('sort')->setLimit(10)->setSigned(false)->setNull(false)->setDefault(0)->setComment('排序权重'))
            ->addColumn(Column::integer('pid')->setLimit(10)->setSigned(false)->setNull(false)->setComment('父级ID'))
            ->addColumn(Column::string('title', 64)->setNull(false)->setComment('标题'))
            ->addColumn(Column::string('icon', 128)->setNull(true)->setComment('图标'))
            ->addColumn(Column::string('url', 200)->setNull(false)->setComment('页面地址'))
            ->addColumn(Column::string('node', 200)->setNull(true)->setComment('权限节点'))
            ->addColumn(Column::string('params', 200)->setNull(true)->setComment('参数'))
            ->addColumn(Column::dateTime('create_time')->setNull(false)->setComment('创建时间'))
            ->addColumn(Column::boolean('status')->setSigned(false)->setNull(false)->setDefault(1)->setComment('状态'))
            ->addColumn(Column::dateTime('delete_time')->setNull(true)->setComment('软删除标记'));
        $table->create();

        $table->insert([
            [
              'id' => 1,
              'sort' => 1,
              'pid' => 0,
              'title' => 'system_manage',
              'icon' => 'tools',
              'url' => '#',
              'node' => NULL,
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ], 
            [
              'id' => 2,
              'sort' => 8,
              'pid' => 1,
              'title' => 'system_user',
              'icon' => 'user-filled',
              'url' => '/system/user',
              'node' => 'systemuser/list',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ], 
            [
              'id' => 3,
              'sort' => 9,
              'pid' => 1,
              'title' => 'system_role',
              'icon' => 'role',
              'url' => '/system/role',
              'node' => 'systemrole/list',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ], 
            [
              'id' => 4,
              'sort' => 10,
              'pid' => 1,
              'title' => 'system_menu',
              'icon' => 'nested',
              'url' => '/system/menu',
              'node' => 'systemmenu/list',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ], 
            [
              'id' => 5,
              'sort' => 0,
              'pid' => 1,
              'title' => 'action_log',
              'icon' => 'tickets',
              'url' => '/system/actionlog',
              'node' => 'systemactionlog/list',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ], 
            [
              'id' => 6,
              'sort' => 0,
              'pid' => 1,
              'title' => 'error_log',
              'icon' => 'warning',
              'url' => '/system/errorlog',
              'node' => 'systemerrorlog/list',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ],
            [
              'id' => 7,
              'sort' => 0,
              'pid' => 1,
              'title' => 'system_config',
              'icon' => 'setting',
              'url' => '/system/config',
              'node' => 'systemconfig/list',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ],
            [
              'id' => 8,
              'sort' => 0,
              'pid' => 1,
              'title' => 'ststem_task',
              'icon' => 'task',
              'url' => '/system/task',
              'node' => 'systemtask/list',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ],
            [
              'id' => 9,
              'sort' => 0,
              'pid' => 0,
              'title' => 'recycle',
              'icon' => 'recycle',
              'url' => '#',
              'node' => NULL,
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ],
            [
              'id' => 10,
              'sort' => 0,
              'pid' => 9,
              'title' => 'system_user',
              'icon' => 'user-filled',
              'url' => '/recycle/systemUser',
              'node' => 'systemuser/recycleList',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ],
            [
              'id' => 11,
              'sort' => 0,
              'pid' => 9,
              'title' => 'system_role',
              'icon' => 'role',
              'url' => '/recycle/systemRole',
              'node' => 'systemrole/recycleList',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ],
            [
              'id' => 12,
              'sort' => 0,
              'pid' => 9,
              'title' => 'system_menu',
              'icon' => 'nested',
              'url' => '/recycle/systemMenu',
              'node' => 'systemmenu/recycleList',
              'params' => NULL,
              'create_time' => date('Y-m-d H:i:s'),
              'status' => 1,
              'delete_time' => NULL,
            ],
        ]);
        $table->saveData();
    }
}
