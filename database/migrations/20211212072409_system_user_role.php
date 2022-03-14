<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemUserRole extends Migrator
{
    public function change()
    {
        $table = $this->table('system_user_role', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统用户角色表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(Column::integer('id')->setLimit(10)->setSigned(false)->setIdentity(true)->setNull(false)->setComment('id'))
            ->addColumn(Column::integer('suid')->setLimit(10)->setSigned(false)->setNull(false)->setComment('系统用户ID'))
            ->addColumn(Column::integer('srid')->setLimit(10)->setSigned(false)->setNull(false)->setComment('系统角色ID'));
        $table->create();
        
        $table->insert([
            [
                'suid' => 1,
                'srid' => 1
            ]
        ])->saveData();
    }
}
