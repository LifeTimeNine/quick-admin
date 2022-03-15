<?php

return [
    'server' => [
        'port' => 9503, // 端口
        'task_worker_num' => 10, // 任务工作进程数
        'pid_file' => runtime_path('system_task') . '.pid', // PID文件地址
    ]
];