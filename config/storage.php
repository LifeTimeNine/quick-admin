<?php

return [
    // 默认存储驱动
    'default' => env('storage.type', 'local'),
    // 允许的文件后缀
    'allow_exts' => ['jpg','png','gif', 'mp4','pdf'],
    // 存储方式
    'storages' => [
        'local' => [
            // 类型
            'type' => 'local',
            // 存储路径
            'root' => app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR .'storage',
            // 外部访问路径
            'access_url' => '/storage',
            // 切片大小 (MB)
            'part_size' => 5,
            // 临时存储路径
            'temp_path' => app()->getRuntimePath() . 'storage' . DIRECTORY_SEPARATOR,
        ],
        'qiniu' => [
            'type' => 'qiniu',
            'access_key' => env('storage.qiniu.access_key', ''),
            'secret_key' => env('storage.qiniu.secret_key', ''),
            'region_id' => env('storage.qiniu.region_id', ''),
            'bucket_name' => env('storage.qiniu.bucket_name', ''),
            // 访问域名 必须设置
            'access_domain' => env('storage.qiniu.access_domain', ''),
            'is_ssl' => env('storage.qiniu.is_ssl', false),
            // 切片大小 (MB)
            'part_size' => env('storage.qiniu.part_size', 5),
        ],
        'ali' => [
            'type' => 'ali',
            // 访问Key ID
            'access_key_id' => env('storage.ali.access_key_id', ''),
            // 访问key 秘钥
            'access_key_secret' => env('storage.ali.access_key_secret', ''),
            // 区域ID
            'region_id' => env('storage.ali.region_id', ''),
            // 默认空间名称
            'bucket_name' => env('storage.ali.bucket_name', ''),
            // 访问域名
            'access_domain' => env('storage.ali.access_domain', ''),
            // 是否使用HTTPS
            'is_https' => env('storage.ali.is_https', false),
            // 切片大小 (MB)
            'part_size' => env('storage.ali.part_size', 5),
        ]
    ]
];