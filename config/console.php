<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        \command\system\Task::class,
        \command\make\Model::class,
        \command\make\Controller::class,
        \command\make\Validate::class,
        \command\backup\Db::class,
    ],
];
