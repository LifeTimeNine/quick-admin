<?php

use Phinx\Db\Table\Index;
use think\migration\Migrator;
use think\migration\db\Column;

class SystemActionLog extends Migrator
{
    public function change()
    {
        $this->table('system_action_log', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统操作记录表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(
                Column::bigInteger('id')->setLimit(20)->setSigned(false)->setNull(false)->setIdentity(true)->setComment('ID')
            )
            ->addColumn(
                Column::integer('suid')->setLimit(10)->setSigned(false)->setNull(false)->setComment('系统用户ID')
            )
            ->addColumn(
                Column::string('node', 100)->setNull(false)->setComment('访问节点')
            )
            ->addColumn(
                Column::dateTime('request_time')->setNull(false)->setComment('请求参数')
            )
            ->addColumn(
                Column::json('request_param')->setNull(false)->setComment('请求参数')
            )
            ->addColumn(
                Column::string('request_ip', 32)->setNull(false)->setComment('请求IP')
            )
            ->addColumn(
                Column::integer('response_code')->setLimit(5)->setSigned(false)->setNull(false)->setComment('响应状态码')
            )
            ->addColumn(
                Column::text('response_content')->setNull(false)->setComment('响应内容')
            )
            ->addColumn(
                Column::string('run_time', 32)->setNull(false)->setComment('运行时间')
            )
            ->addIndex(
                (new Index)->setName('suid')->setColumns(['suid'])
            )
            ->addIndex(
                (new Index)->setName('node')->setColumns(['node'])
            )
            ->create();
    }
}
