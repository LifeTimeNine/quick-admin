<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemRole extends Migrator
{
    public function change()
    {
        $table = $this->table('system_role', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统角色表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(Column::integer('id')->setLimit(10)->setSigned(false)->setIdentity(true)->setNull(false)->setComment('id'))
            ->addColumn(Column::string('name', 64)->setNull(false)->setComment('名称'))
            ->addColumn(Column::string('desc', 200)->setNull(true)->setComment('描述'))
            ->addColumn(Column::integer('create_suid')->setLimit(10)->setSigned(false)->setNull(false)->setComment('创建用户ID'))
            ->addColumn(Column::dateTime('create_time')->setNull(false)->setComment('创建时间'))
            ->addColumn(Column::boolean('status')->setSigned(false)->setNull(false)->setDefault(1)->setComment('状态'))
            ->addColumn(Column::dateTime('delete_time')->setNull(true)->setComment('软删除标记'));
        $table->create();

        $table->insert([
            [
                'name' => '超级管理员',
                'create_suid' => 0,
                'create_time' => date('Y-m-d H:i:s')
            ]
        ]);
        $table->saveData();
    }
}
