<?php

/**
 * 各模块 token 相关配置
 */

return [
    // 加密盐
    'salt' => 'admin_123456',
    // 应用单独设置
    'apps' => [
        'admin' => [
            //  Token有效时间
            'expire' => 3600 * 24 * 7,
            // 是否自动刷新token
            'auto_refresh' => true,
            // 自动刷新token的剩余时间占比
            'auto_refresh_time_ratio' => 0.1,
        ]
    ],
];