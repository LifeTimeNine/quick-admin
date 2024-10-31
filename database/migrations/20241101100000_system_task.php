<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemTask extends Migrator
{
    public function change()
    {
        $this->table('system_task', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统任务表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(
                Column::integer('id')->setLimit(10)->setNull(false)->setSigned(false)->setIdentity(true)->setComment('id')
            )
            ->addColumn(
                Column::string('title')->setLimit(128)->setNull(false)->setComment('任务名称')
            )
            ->addColumn(
                Column::string('exec_file')->setLimit(512)->setNull(false)->setComment('任务指令')
            )
            ->addColumn(
                Column::string('args')->setLimit(1000)->setNull(true)->setComment('任务参数')
            )
            ->addColumn(
                Column::tinyInteger('type')->setLimit(1)->setSigned(false)->setNull(false)->setComment('任务类型')
            )
            ->addColumn(
                Column::string('cron')->setLimit(256)->setNull(true)->setComment('定时参数')
            )
            ->addColumn(
                Column::dateTime('create_time')->setNull(false)->setComment('创建时间')
            )
            ->addColumn(
                Column::tinyInteger('exec_status')->setLimit(1)->setSigned(false)->setNull(false)->setDefault(1)->setComment('执行状态 （1等待中，2执行中）')
            )
            ->addColumn(
                Column::dateTime('last_exec_time')->setNull(true)->setComment('最后执行时间')
            )
            ->addColumn(
                Column::tinyInteger('last_exec_result')->setLimit(1)->setSigned(false)->setNull(true)->setComment('最后一次执行结果')
            )
            ->addColumn(
                Column::dateTime('next_exec_time')->setNull(true)->setComment('下一次执行时间')
            )
            ->addColumn(
                Column::unsignedInteger('exec_num')->setLimit(10)->setNull(false)->setDefault(0)->setComment('执行次数')
            )
            ->addColumn(
                Column::unsignedInteger('success_num')->setLimit(10)->setNull(false)->setDefault(0)->setComment('成功次数')
            )
            ->addColumn(
                Column::unsignedInteger('fail_num')->setLimit(10)->setNull(false)->setDefault(0)->setComment('失败次数')
            )
            ->addColumn(
                Column::tinyInteger('status')->setLimit(1)->setSigned(false)->setDefault(1)->setComment('状态')
            )
            ->create();
    }
}
