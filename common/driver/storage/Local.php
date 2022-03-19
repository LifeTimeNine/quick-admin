<?php

declare (strict_types = 1);

namespace driver\storage;

use think\facade\Route;
use tools\Tools;

/**
 * 本地存储
 */
class Local extends Driver
{
    /**
     * 表单文件Key
     * @var string
     */
    protected $fileKey = 'file';

    /**
     * 缓存前缀
     * @var string
     */
    protected $cachePrefix = 'upload_token_';

    /**
     * 已上传的
     */
    public function info(string $fileName, string $fileMd5): array
    {
        $this->app->cache->set("{$this->cachePrefix}{$fileMd5}", [
            'file_name' => $fileName,
            'time' => time()
        ], 600);
        return [
            'server' => Route::buildUrl('upload/file')->suffix(false)->domain(true)->build(),
            'method' => 'POST',
            'header' => [
                [
                    'key' => 'content-type',
                    'value' => 'multipart/form-data'
                ],
            ],
            'body' => [
                [
                    'key' => 'token',
                    'value' => $fileMd5
                ],
            ],
            'file_key' => $this->fileKey
        ];
    }

    public function has(string $fileName, string $fileMd5): bool
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);

        return file_exists($this->getConfig('root') . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $name . '.' . $ext);
    }

    public function getAccessUrl(string $fileName, string $fileMd5): string
    {
        [$dir, $name, $ext] = $this->getPathInfo($fileName, $fileMd5);

        return $this->app->request->domain() . $this->getConfig('access_url') . "/{$dir}/{$name}.{$ext}";
    }

    /**
     * 保存文件
     * @access  public
     * @return  bool|string
     */
    public function saveFile()
    {
        $token = $this->app->request->post('token');
        if (empty($cacheData = $this->app->cache->get("{$this->cachePrefix}{$token}"))) return 'Token 验证失败';
        /** @var \think\File $file */
        $file = $this->app->request->file($this->fileKey);
        if (empty($file)) return '文件不存在';
        
        [$dir, $name, $ext] = $this->getPathInfo($cacheData['file_name'], $token);
        
        try {
            $file->move($this->getConfig('root') . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR, "{$name}.{$ext}");
            $this->app->cache->delete("{$this->cachePrefix}{$token}");
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        return true;
    }


    public function partInfo(string $fileName, string $fileMd5): array
    {
        $this->app->cache->set("{$this->cachePrefix}{$fileMd5}", [
            'file_name' => $fileName,
            'time' => time()
        ], 3600 * 24 * 7);
        $this->app->cache->set("{$this->cachePrefix}{$fileMd5}_parts", [], 3600 * 24 * 7);
        return [
            'upload_id' => $fileMd5,
            'part_size' => $this->getConfig('part_size'),
        ];
    }

    public function partOptions(string $fileName, string $fileMd5, string $uploadId, int $partNumner): array
    {
        if (!$this->app->cache->has("{$this->cachePrefix}{$uploadId}")) {
            return 'UploadId 不存在';
        }
        return [
            'part_number' => $partNumner,
            'server' => Route::buildUrl('upload/part', ['uploadId'=>$uploadId, 'partNumber'=>$partNumner])->suffix(false)->domain(true)->build(),
            'method' => 'PUT',
            'header' => [
                [
                    'key' => 'token',
                    'value' => sha1($uploadId . $partNumner . $uploadId),
                ]
            ],
        ];
    }

    public function partList(string $fileName, string $fileMd5, string $uploadId): array
    {
        $res = [];
        $partList = $this->app->cache->get("{$this->cachePrefix}{$uploadId}_parts") ?? [];
        foreach($partList as $item) {
            [$partNumber, $etag] = explode('/', $item);
            $res[] = [
                'partNumber' => $partNumber,
                'etag' => $etag
            ];
        }
        return $res;
    }

    public function part()
    {
        $uploadId = $this->app->request->get('uploadId');
        $partNumber = $this->app->request->get('partNumber');
        $auth = $this->app->request->header('token');
        if ($auth <> sha1($uploadId . $partNumber . $uploadId)) {
            return "Authorization 验证失败";
        }
        $etag = strtoupper(md5($uploadId . $partNumber));
        $tempPath = $this->getConfig('temp_path') ?: $this->app->getRuntimePath() . 'storage' . DIRECTORY_SEPARATOR;
        $tempPath .= $uploadId . DIRECTORY_SEPARATOR;
        if (!is_dir($tempPath)) mkdir($tempPath, 0777, true);

        file_put_contents("{$tempPath}/{$partNumber}.tmp", $this->app->request->getInput());

        $this->app->cache->push("{$this->cachePrefix}{$uploadId}_parts", "{$partNumber}/{$etag}");
        return [
            'etag' => $etag,
        ];
    }

    public function partComplete(string $fileName, string $fileMd5, string $uploadId, array $parts)
    {
        if (empty($cacheData = $this->app->cache->get("{$this->cachePrefix}{$uploadId}"))) return 'UploadId 不存在';

        [$dir, $name, $ext] = $this->getPathInfo($cacheData['file_name'], $uploadId);

        $dir = $this->getConfig('root') . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $tempPath = $this->getConfig('temp_path') ?: $this->app->getRuntimePath() . 'storage' . DIRECTORY_SEPARATOR;
        $tempPath .= $uploadId . DIRECTORY_SEPARATOR;

        $file = new \SplFileObject("{$dir}/{$name}.{$ext}", 'wb');
        foreach($parts as $part) {
            $partData = @file_get_contents("{$tempPath}/{$part['partNumber']}.tmp");
            if ($partData == false) return "文件合并失败";
            $file->fwrite($partData);
        }
        Tools::instance()->delDir($tempPath);
        return true;
    }
}