<?php

/**
 * 各模块 token 相关配置
 */

return [
    'admin' => [
        'iss' => 'admin',
        'salt' => 'jwt_admin_123456',
        'exp' => 3600 * 24 * 30
    ],
];