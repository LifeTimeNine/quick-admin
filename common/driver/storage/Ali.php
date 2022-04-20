<?php

namespace driver\storage;

use lang\Variable;
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
     * 缓存前缀
     * @var string
     */
    protected $cachePrefix = 'upload_ali_upload_id_';

    /**
     * 获取Object实例
     * @access  protected
     * @return  \service\ali\oss\object\Basics
     */
    protected function getObject()
    {
        return Basics::instance([
            'accessKey_id' => $this->getConfig('accessKey_id'),
            'accessKey_secret' => $this->getConfig('accessKey_secret'),
            'oss_endpoint' => $this->getConfig('endpoint'),
            'oss_bucketName' => $this->getConfig('bucketName'),
        ]);
    }

    /**
     * 获取切片上传实例
     * @access  protected
     * @return  \service\ali\oss\object\Multipart
     */
    protected function getMultipart()
    {
        return Multipart::instance([
            'accessKey_id' => $this->getConfig('accessKey_id'),
            'accessKey_secret' => $this->getConfig('accessKey_secret'),
            'oss_endpoint' => $this->getConfig('endpoint'),
            'oss_bucketName' => $this->getConfig('bucketName'),
        ]);
    }

    /**
     * 获取 Bucket 实例
     * @access  protected
     * @return  \service\ali\oss\bucket\Basics
     */
    protected function getBucket()
    {
        return BucketBasics::instance([
            'accessKey_id' => $this->getConfig('accessKey_id'),
            'accessKey_secret' => $this->getConfig('accessKey_secret'),
            'oss_endpoint' => $this->getConfig('endpoint'),
            'oss_bucketName' => $this->getConfig('bucketName'),
        ]);
    }

    public function info(string $fileName, string $fileMd5): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->getObject()->webPut('', "{$dir}/{$name}.{$ext}");
        return [
            'server' => $options['url'],
            'method' => 'POST',
            'header' => $options['header'],
            'body' => $options['body'],
            'file_key' => $options['fileFieldName']
        ];
    }

    public function has(string $fileName, string $fileMd5): bool
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $data = $this->getObject()->head('', "{$dir}/{$name}.{$ext}");
        return $data !== false;
    }

    public function getAccessUrl(string $fileName, string $fileMd5): string
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        if ($this->getConfig('access_domain')) {
            return ($this->getConfig('is_ssl') ? 'https://' : 'http://') . "{$this->getConfig('access_domain')}/{$dir}/{$name}.{$ext}";
        } else {
            return ($this->getConfig('is_ssl') ? 'https://' : 'http://') . "{$this->getConfig('bucketName')}.{$this->getConfig('endpoint')}/{$dir}/{$name}.{$ext}";
        }
    }

    public function partInfo(string $fileName, string $fileMd5): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->getMultipart()->init('', "{$dir}/{$name}.{$ext}");
        return [
            'upload_id' => $options['UploadId'],
            'part_size' => $this->getConfig('part_size'),
        ];
    }

    public function partOptions(string $fileName, string $fileMd5, string $uploadId, int $partNumner): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->getObject()->webPut('', "{$uploadId}/{$partNumner}.tmp");
        return [
            'part_number' => $partNumner,
            'server' => $options['url'],
            'method' => 'POST',
            'header' => $options['header'],
            'body' => $options['body'],
            'file_key' => $options['fileFieldName']
        ];
    }

    public function partList(string $fileName, string $fileMd5, string $uploadId): array
    {
        $list = $this->getBucket()->getV2('','', '', '', '', 1000, $uploadId);
        $res = [];
        foreach ($list['Contents'] as $item) {
            $res[] = [
                'partNumber' => pathinfo(urldecode($item['Key']), PATHINFO_FILENAME),
                'etag' => $item['ETag'],
            ];
        }
        return $res;
    }

    public function partComplete(string $fileName, string $fileMd5, string $uploadId, array $parts)
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        try {
            $deleteFiles = [];
            $etags = [];
            foreach($parts as $part) {
                $deleteFiles[] = "{$uploadId}/{$part['partNumber']}.tmp";
                $res = $this->getMultipart()->copy('', "{$dir}/{$name}.{$ext}", $part['partNumber'], $uploadId, '', "{$uploadId}/{$part['partNumber']}.tmp");
                $etags[$part['partNumber']] = trim($res['ETag'], '"');
            }
            $this->getMultipart()->complete('', "{$dir}/{$name}.{$ext}", $uploadId, $etags);
            $this->getObject()->deleteMultiple('', $deleteFiles);
        } catch (\Throwable $th) {
            return Lang::get(Variable::FILE_COMPLETE_FAil);
        }
        return true;
    }
}