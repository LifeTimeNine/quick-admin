<?php

declare (strict_types = 1);

namespace driver\storage;

use think\helper\Arr;

/**
 * 存储驱动基类
 */
abstract class Driver
{
    /**
     * 当前应用信息
     * @var \think\App
     */
    protected $app;

    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * 构造函数
     */
    public function __construct(\think\App $app, array $config = [])
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * 获取配置
     * @access  protected
     * @param   string  $name       名称
     * @param   mixed   $default    默认值
     * @return  mixed
     */
    protected function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return Arr::get($this->config, $name, $default);
        }
        return $this->config;
    }

    /**
     * 获取文件路径信息
     * @access  protected
     * @param   string     $fileName   文件名称
     * @param   string     $fileMd5    文件Md5值
     * @return  array
     */
    protected function getPathInfo(string $fileName, string $fileMd5)
    {
        return [
            substr($fileMd5, 0, 2),
            substr($fileMd5, 2),
            pathinfo($fileName, PATHINFO_EXTENSION)
        ];
    }

    /**
     * 获取文件上传参数
     * @access  public
     * @param   string     $fileName   文件名称
     * @param   string     $fileMd5    文件Md5值
     * @return  array
     */
    abstract public function info(string $fileName, string $fileMd5): array;

    /**
     * 判断文件是否存在
     * @access  public
     * @param   string     $fileName   文件名称
     * @param   string     $fileMd5    文件Md5值
     * @return  bool
     */
    abstract public function has(string $fileName, string $fileMd5): bool;

    /**
     * 获取文件访问地址
     * @access  public
     * @param   string     $fileName   文件名称
     * @param   string     $fileMd5    文件Md5值
     * @return  string
     */
    abstract public function getAccessUrl(string $fileName, string $fileMd5): string;

    /**
     * 获取切片上传参数
     * @access  public
     * @param   string  $fileName   文件名称
     * @param   string  $fileMd5    文件MD5值
     * @return  array
     */
    abstract public function partInfo(string $fileName, string $fileMd5): array;

    /**
     * 获取单个切片参数
     * @access  public
     * @param   string  $fileName   文件名称
     * @param   string  $fileMd5    文件md5
     * @param   string  $uploadId   上传ID
     * @param   int     $partNumner 标记
     * @return  array
     */
    abstract public function partOptions(string $fileName, string $fileMd5, string $uploadId, int $partNumner): array;

    /**
     * 获取已上传的切片列表
     * @access  public
     * @param   string  $fileName   文件名称
     * @param   string  $fileMd5    文件md5
     * @param   string  $uploadId   上传ID
     * @return  array
     */
    abstract public function partList(string $fileName, string $fileMd5, string $uploadId): array;

    /**
     * 完成切片上传
     * @param   string  $fileName   文件名称
     * @param   string  $fileMd5    文件md5
     * @param   string  $uploadId   上传ID
     * @param   array   $parts      切片信息列表
     * @return  string|bool
     */
    abstract public function partComplete(string $fileName, string $fileMd5, string $uploadId, array $parts);
}