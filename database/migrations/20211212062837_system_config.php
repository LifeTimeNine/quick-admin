<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemConfig extends Migrator
{
    public function change()
    {
        $table = $this->table('system_config', ['id'=>false,'primary_key'=>'id', 'comment'=>'系统配置表','collation'=>'utf8mb4_general_ci'])
            ->addColumn(
                Column::bigInteger('id')->setLimit(20)->setSigned(false)->setNull(false)->setIdentity(true)->setComment('ID')
            )
            ->addColumn(
                Column::string('key', 100)->setNull(false)->setComment('键')
            )
            ->addColumn(
                Column::text('value')->setComment('值')
            )
            ->addColumn(
                Column::boolean('type')->setSigned(false)->setNull(false)->setDefault(1)->setComment('类型')
            )
            ->addColumn(
                Column::string('name', 200)->setComment('配置名称')
            );
        $table->create();
        
        $table->insert([
            'key' => 'system_name',
            'value' => 'QuickAdmin',
            'type' => 1,
            'name' => '系统名称'
        ]);

        $table->saveData();
    }
}
