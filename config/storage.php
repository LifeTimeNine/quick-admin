<?php

return [
    // 默认存储驱动
    'default' => 'local',
    // 允许的文件后缀
    'allow_exts' => ['jpg','png','gif', 'mp4'],
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
            'access_key' => '',
            'secret_key' => '',
            'region' => '华南',
            'bucket_name' => '',
            // 访问域名 必须设置
            'access_domain' => '',
            'is_ssl' => true,
            // 切片大小 (MB)
            'part_size' => 5,
        ],
        'ali' => [
            'type' => 'ali',
            'accessKey_id' => '',
            'accessKey_secret' => '',
            'endpoint' => 'oss-cn-beijing.aliyuncs.com',
            'bucketName' => '',
            // 访问域名 可选
            'access_domain' => '',
            'is_ssl' => true,
            // 切片大小 (MB)
            'part_size' => 5,
        ]
    ]
];