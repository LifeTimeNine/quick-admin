<?php

namespace driver\storage;

use lang\Variable;
use service\qiniu\storage\Objects;
use think\facade\Lang;

/**
 * 七牛云存储
 */
class Qiniu extends Driver
{
    /**
     * 获取Object实例
     * @access  protected
     * @return  \service\qiniu\storage\Objects
     */
    protected function getObject()
    {
        return Objects::instance([
            'accessKey' => $this->getConfig('access_key'),
            'secretKey' => $this->getConfig('secret_key'),
            'storage_region' => $this->getConfig('region'),
            'storage_bucketName' => $this->getConfig('bucket_name'),
            'storage_domain' => $this->getConfig('access_domain'),
        ]);
    }
    public function info(string $fileName, string $fileMd5): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->getObject()->WebUpload('', "{$dir}/{$name}.{$ext}");
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
        try {
            $res = $this->getObject()->getMetaData('', "{$dir}/{$name}.{$ext}");
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    public function getAccessUrl(string $fileName, string $fileMd5): string
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        return ($this->getConfig('is_ssl') ? 'https://' : 'http://') . "{$this->getConfig('access_domain')}/{$dir}/{$name}.{$ext}";
    }

    public function partInfo(string $fileName, string $fileMd5): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $info = $this->getObject()->initMultipart('', "{$dir}/{$name}.{$ext}");
        return [
            'upload_id' => $info['uploadId'],
            'part_size' => $this->getConfig('part_size'),
        ];
    }

    public function partOptions(string $fileName, string $fileMd5, string $uploadId, int $partNumner): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $options = $this->getObject()->webPartParams('', "{$dir}/{$name}.{$ext}", $uploadId, $partNumner);
        return [
            'part_number' => $partNumner,
            'server' => $options['url'],
            'method' => 'PUT',
            'header' => $options['header']
        ];
    }
    public function partList(string $fileName, string $fileMd5, string $uploadId): array
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);
        $res = [];
        try {
            $partList = $this->getObject()->partList('', "{$dir}/{$name}.{$ext}", $uploadId);
            foreach($partList['parts'] as $item) {
                $res[] = [
                    'partNumber' => $item['partnumber'],
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
            $res = $this->getObject()->completePart('', "{$dir}/{$name}.{$ext}", $uploadId, $partsArr);
        } catch (\Throwable $th) {
            return Lang::get(Variable::FILE_COMPLETE_FAil);
        }
        return true;
    }
}