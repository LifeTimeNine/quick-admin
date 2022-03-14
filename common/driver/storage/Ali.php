<?php

namespace driver\storage;

use service\ali\oss\object\Basics;

/**
 * 阿里云存储
 */
class Ali extends Driver
{
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
}