<?php

namespace command\backup;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use Swoole\Coroutine\System;
use function Swoole\Coroutine\run;

/**
 * 备份数据库
 */
class Db extends Command
{
    protected function configure()
    {
        $this->setName('backup:db')
            ->addOption('connection', 'c', Option::VALUE_OPTIONAL, '连接配置信息名称')
            ->addOption('dir', 'd', Option::VALUE_OPTIONAL, '保存目录', root_path('database/backup'))
            ->addOption('filename', 'f', Option::VALUE_OPTIONAL, '文件名称')
            ->setDescription('Back up the specified database');
    }

    protected function execute(Input $input, Output $output)
    {
        $connection = $this->app->config->get('database.default');
        $database = $this->app->config->get("database.connections.{$connection}.database");
        $connection = $input->getOption('connection') ?: $connection;
        $dir = $input->getOption('dir');
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $filename = $input->getOption('filename') ?: $database;
        $hostname = $this->app->config->get("database.connections.{$connection}.hostname");
        $username = $this->app->config->get("database.connections.{$connection}.username");
        $password = $this->app->config->get("database.connections.{$connection}.password");

        $command = "mysqldump -h{$hostname} -u{$username} -p{$password} {$database} > {$dir}/{$filename}.sql";
        run(function() use($command, $output, $dir, $filename) {
            $res = System::exec($command);
            if ($res['code'] == 0 && $res['signal'] == 0) {
                $output->info("Backup success {$dir}/{$filename}.sql");
            } else {
                $output->error($res['output']);
            }
        });
    }
}