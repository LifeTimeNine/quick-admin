<?php

/**
 * 各模块 token 相关配置
 */

return [
    // 加密盐
    'salt' => 'admin_123456',
    // 刷新 加密盐
    'refresh_salt' => 'admin_refresh_123456',

    'apps' => [
        'index' => [
            //  Token有效时间
            'exp' => 3600 * 24 * 7,
            // 刷新Token 有效时间
            'refressh_exp' => 3600 * 24 * 3
        ]
    ],
];