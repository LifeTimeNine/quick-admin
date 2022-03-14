<?php

namespace command\system;

use service\SystemTask;
use swoole\server\Http;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

/**
 * 系统任务
 */
class Task extends Command
{
    protected function configure()
    {
        $this->setName('system:task')
            ->addArgument('action', Argument::OPTIONAL, 'action', 'start')
            ->addOption('daemonize', 'd', Option::VALUE_NONE, '已守护进程的方式运行')
            ->setDescription('System Task service');
    }

    protected function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $config = SystemTask::instance()->getServerConfig();
        if ($input->hasOption('daemonize')) {
            $config->setDaemonize(true);
        }
        $config->setEventClass(TaskEvent::class);
        $server = Http::instance($config);
        if ($action == 'start') {
            $server->initServer();
            $server->getServer()->on('task',[TaskEvent::class, 'onTaskCoroutine']);
            $server->start();
        } elseif ($action == 'restart') {
            $server->restart(function($server) {
                $server->getServer()->on('task',[TaskEvent::class, 'onTaskCoroutine']);
            });
        } else {
            $server->$action();
        }
        
    }
}