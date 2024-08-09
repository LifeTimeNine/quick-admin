<?php

namespace driver\storage;

use lang\Variable;
use lifetime\bridge\ali\oss\Objects;
use service\ali\oss\bucket\Basics as BucketBasics;
use service\ali\oss\object\Basics;
use service\ali\oss\object\Multipart;
use think\facade\Lang;

/**
 * 阿里云存储
 */
class Ali extends Driver
{
    /**
     * 配置
     * @var array
     */
    protected $config = [
        // 访问Key ID
        'access_key_id' => '',
        // 访问key 秘钥
        'access_key_secret' => '',
        // 区域ID
        'region_id' => '',
        // 默认空间名称
        'bucket_name' => '',
        // 访问域名
        'access_domain' => '',
        // 是否使用HTTPS
        'is_https' => true,
        // 是否内网访问
        'internal_access'=> false,
        // 切片大小 (MB)
        'part_size' => 5,
    ];

    /**
     * 阿里云对象存储实例
     * @var Objects
     */
    protected $object;

    public function __construct(\think\App $app, array $config = [])
    {
        parent::__construct($app, $config);
        $this->object = new Objects($this->config);
    }

    public function info(string $fileName, string $fileMd5): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->object->post("{$dir}/{$name}.{$ext}");
        return [
            'server' => $options['url'],
            'method' => $options['method'],
            'content_type' => $options['content_type'],
            'header' => $options['header'],
            'query' => $options['query'],
            'body' => $options['body'],
            'file_key' => $options['file_key']
        ];
    }

    public function has(string $fileName, string $fileMd5): bool
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        try {
            $this->object->getHead("{$dir}/{$name}.{$ext}");
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function getAccessUrl(string $fileName, string $fileMd5): string
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        return $this->object->getAccessPath("{$dir}/{$name}.{$ext}");
    }

    public function partInfo(string $fileName, string $fileMd5): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->object->initPart("{$dir}/{$name}.{$ext}");
        return [
            'upload_id' => $options['UploadId'],
            'part_size' => $this->getConfig('part_size'),
        ];
    }

    public function partOptions(string $fileName, string $fileMd5, string $uploadId, int $partNumber): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->object->clientUploadPart("{$dir}/{$name}.{$ext}", $uploadId, $partNumber);
        return [
            'server' => $options['url'],
            'method' => $options['method'],
            'content_type' => $options['content_type'],
            'header' => $options['header'],
            'query' => $options['query'],
            'body' => $options['body'] ?? [],
            'part_number' => $options['part_number'],
        ];
    }

    public function partList(string $fileName, string $fileMd5, string $uploadId): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $list = $this->object->partList("{$dir}/{$name}.{$ext}", $uploadId);
        $res = [];
        foreach ($list['Part'] as $item) {
            $res[] = [
                'part_number' => $item['PartNumber'],
                'etag' => $item['ETag'],
            ];
        }
        return $res;
    }

    public function partComplete(string $fileName, string $fileMd5, string $uploadId, array $parts)
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $partsArr = [];
        foreach($parts as $item) $partsArr[$item['partNumber']] = $item['etag'];
        try {
            $this->object->completePart("{$dir}/{$name}.{$ext}", $uploadId, $partsArr);
        } catch (\Throwable $th) {
            return Lang::get(Variable::FILE_COMPLETE_FAil);
        }
        return true;
    }
}