<?php

use Phinx\Db\Table\Index;
use think\migration\Migrator;
use think\migration\db\Column;
use think\migration\db\Table;

class SystemUser extends Migrator
{
    public function change()
    {
        $table = $this->table('system_user', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统用户表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(Column::integer('id')->setLimit(10)->setNull(false)->setIdentity(true)->setSigned(false)->setComment('id'))
            ->addColumn(Column::string('username', 64)->setNull(false)->setComment('用户名'))
            ->addColumn(Column::string('password', 32)->setNull(false)->setComment('密码'))
            ->addColumn(Column::string('avatar')->setNull(true)->setComment('头像'))
            ->addColumn(Column::string('name', 32)->setNull(true)->setComment('姓名'))
            ->addColumn(Column::string('desc', 200)->setNull(true)->setComment('描述'))
            ->addColumn(Column::dateTime('create_time')->setNull(true)->setComment('创建时间'))
            ->addColumn(Column::boolean('status')->setSigned(false)->setNull(false)->setDefault(1)->setComment('状态'))
            ->addColumn(Column::dateTime('last_login_time')->setNull(true)->setComment('最后登录时间'))
            ->addColumn(Column::integer('last_login_ip')->setLimit(10)->setSigned(false)->setNull(true)->setComment('最后登录IP'))
            ->addColumn(Column::integer('login_num')->setLimit(5)->setSigned(false)->setNull(false)->setDefault(0)->setComment('登录次数'))
            ->addColumn(Column::dateTime('delete_time')->setNull(true)->setComment('软删除标记'))
            ->addIndex((new Index)->setName('username')->setColumns(['username']));
        $table->create();

        $table->insert([
            [
                'username' => 'admin',
                'password' => 'e10adc3949ba59abbe56e057f20f883e',
                'name' => '超级管理员',
                'desc' => '这是一个超级管理员账户',
                'create_time' => date('Y-m-d H:i:s')
            ]
        ])->saveData();
    }
}
