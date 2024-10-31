<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemTaskLog extends Migrator
{
    public function change()
    {
        $this->table('system_task_log', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统任务日志表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(
                Column::integer('id')->setLimit(10)->setNull(false)->setSigned(false)->setIdentity(true)->setComment('id')
            )
            ->addColumn(
                Column::unsignedInteger('stid')->setLimit(10)->setNull(false)->setComment('系统任务ID')
            )
            ->addColumn(
                Column::date('start_time')->setNull(false)->setComment('开始时间')
            )
            ->addColumn(
                Column::date('end_time')->setNull(false)->setComment('结束时间')
            )
            ->addColumn(
                Column::string('runtime', 15)->setNull(false)->setComment('运行时间')
            )
            ->addColumn(
                Column::longText('out')->setNullable()->setComment('标准输出内容')
            )
            ->addColumn(
                Column::longText('err')->setNullable()->setComment('异常输出内容')
            )
            ->addColumn(
                Column::tinyInteger('result')->setLimit(1)->setUnsigned()->setNull(false)->setComment('执行结果')
            )
            ->create();
    }
}
