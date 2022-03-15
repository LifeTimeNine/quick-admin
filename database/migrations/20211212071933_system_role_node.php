<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemRoleNode extends Migrator
{
    public function change()
    {
        $this->table('system_role_node', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统角色权限节点表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(Column::integer('id')->setLimit(10)->setSigned(false)->setIdentity(true)->setNull(false)->setComment('id'))
            ->addColumn(Column::integer('srid')->setLimit(10)->setSigned(false)->setNull(false)->setComment('系统角色ID'))
            ->addColumn(Column::string('node', 100)->setNull(false)->setComment('权限节点'))
            ->create();
    }
}
