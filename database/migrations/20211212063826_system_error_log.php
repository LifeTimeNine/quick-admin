<?php

use Phinx\Db\Table\Index;
use think\migration\Migrator;
use think\migration\db\Column;

class SystemErrorLog extends Migrator
{
    public function change()
    {
        $this->table('system_error_log', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统异记录表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(
                Column::integer('id')->setLimit(10)->setSigned(false)->setNull(false)->setIdentity(true)->setComment('ID')
            )
            ->addColumn(
                Column::string('hash', 200)->setNull(false)->setComment('哈希值')
            )
            ->addColumn(
                Column::string('app_name', 32)->setNull(false)->setComment('应用名称')
            )
            ->addColumn(
                Column::string('path_info', 500)->setNull(false)->setComment('访问地址')
            )
            ->addColumn(
                Column::string('access_ip', 32)->setNull(false)->setComment('访问IP')
            )
            ->addColumn(
                Column::json('request_param')->setComment('请求参数')
            )
            ->addColumn(
                Column::dateTime('request_time')->setNull(false)->setComment('请求时间')
            )
            ->addColumn(
                Column::integer('error_code')->setLimit(10)->setSigned(false)->setNull(false)->setComment('异常码')
            )
            ->addColumn(
                Column::string('error_message', 2000)->setNull(false)->setComment('异常消息')
            )
            ->addColumn(
                Column::string('error_file', 500)->setNull(false)->setComment('异常文件')
            )
            ->addColumn(
                Column::integer('error_line')->setLimit(10)->setNull(false)->setSigned(false)->setComment('异常行数')
            )
            ->addColumn(
                Column::text('error_trace')->setNull(false)->setComment('异常跟踪')
            )
            ->addColumn(
                Column::dateTime('happen_time')->setNull(false)->setComment('第一次发生的时间')
            )
            ->addColumn(
                Column::dateTime('last_happen_time')->setNull(false)->setComment('最后一次发生的时间')
            )
            ->addColumn(
                Column::integer('happen_num')->setLimit(10)->setSigned(false)->setNull(false)->setDefault(1)->setComment('累计发生次数')
            )
            ->addColumn(
                Column::boolean('status')->setSigned(false)->setNull(false)->setDefault(1)->setComment('状态')
            )
            ->addColumn(
                Column::integer('resolve_suid')->setLimit(10)->setSigned(false)->setNull(true)->setComment('处理用户ID')
            )
            ->addColumn(
                Column::dateTime('resolve_time')->setNull(true)->setComment('处理时间')
            )
            ->addIndex(
                (new Index)->setName('hash')->setColumns(['hash'])
            )
            ->create();
    }
}
