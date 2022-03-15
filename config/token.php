<?php

/**
 * 各模块 token 相关配置
 */

return [
    // 加密盐
    'salt' => 'admin_123456',
    // 刷新 加密盐
    'refresh_salt' => 'admin_refresh_123456',
    // 默认有效期
    'default_expire' => 3600 * 24 * 7,
    // 应用单独设置
    'apps' => [
        'index' => [
            //  Token有效时间
            'expire' => 3600 * 24 * 7
        ]
    ],
];