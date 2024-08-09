<?php

namespace driver\storage;

use lang\Variable;
use lifetime\bridge\qiniu\kodo\Objects;
use think\facade\Lang;

/**
 * 七牛云存储
 */
class Qiniu extends Driver
{
    /**
     * 配置
     * @var array
     */
    protected $config = [
        // AccessKey
        'access_key' => '',
        // SecretKey
        'secret_key' => '',
        // 区域ID
        'region_id' => '',
        // 访问域名
        'access_domain' => '',
        // 是否使用SSL
        'is_ssl' => true,
        // 默认Bucket名称
        'bucket_name' => '',
        // 分片大小(MB)
        'part_size' => 5
    ];
    /**
     * 七牛云对象存储实例
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
        $options = $this->object->clientUpload("{$dir}/{$name}.{$ext}");
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
            $res = $this->object->getMetaData("{$dir}/{$name}.{$ext}");
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    public function getAccessUrl(string $fileName, string $fileMd5): string
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        return ($this->config['is_ssl'] ? 'https://' : 'http://') . "{$this->config['access_domain']}/{$dir}/{$name}.{$ext}";
    }

    public function partInfo(string $fileName, string $fileMd5): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $info = $this->object->initPart("{$dir}/{$name}.{$ext}");
        return [
            'upload_id' => $info['uploadId'],
            'part_size' => $this->config['part_size'],
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
        $res = [];
        try {
            $partList = $this->object->partList("{$dir}/{$name}.{$ext}", $uploadId);
            foreach($partList['parts'] as $item) {
                $res[] = [
                    'part_number' => $item['partnumber'],
                    'etag' => $item['etag']
                ];
            }
        } catch (\Throwable $th) {
            return [];
        }
        return $res;
    }

    public function partComplete(string $fileName, string $fileMd5, string $uploadId, array $parts)
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $partsArr = [];
        foreach($parts as $item) $partsArr[$item['partNumber']] = $item['etag'];
        try {
            $res = $this->object->completePart("{$dir}/{$name}.{$ext}", $uploadId, $partsArr);
        } catch (\Throwable $th) {
            return Lang::get(Variable::FILE_COMPLETE_FAil);
        }
        return true;
    }
}