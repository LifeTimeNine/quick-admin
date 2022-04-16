<?php

declare (strict_types = 1);

namespace service;

use lang\Variable;
use think\facade\Lang;
use think\helper\Arr;
use think\Manager;
use traits\tools\Error;
use traits\tools\Instance;

/**
 * 存储管理类
 */
class Storage extends Manager
{
    use Instance,Error;

    protected $namespace = '\\driver\\storage\\';

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->app = app();
    }

    /**
     * 默认驱动
     * @access  public
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return $this->getConfig('default');
    }

    /**
     * 获取存储配置
     * @access  public
     * @param   null|string $name       名称
     * @param   mixed       $default    默认值
     * @return  mixed
     */
    public function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get("storage.{$name}", $default);
        }

        return $this->app->config->get('storage');
    }

    /**
     * 获取驱动配置
     * @access  public
     * @param   string  $storage    驱动类型
     * @param   string  $name       名称
     * @param   mixed   $default    默认值
     * @return  mixed
     */
    public function getStorageConfig(string $storage, string $name = null, $default = null)
    {
        if ($config = $this->getConfig("storages.{$storage}")) {
            return Arr::get($config, $name, $default);
        }

        throw new \InvalidArgumentException("Storage [$storage] not found.");
    }

    protected function resolveType(string $name)
    {
        return $this->getStorageConfig($name, 'type', 'local');
    }

    protected function resolveConfig(string $name)
    {
        return $this->getStorageConfig($name);
    }

    /**
     * 获取驱动对象
     * @access  public
     * @param   string  $name   存储配置名
     * @return  \driver\storage\Driver
     */
    public function storage(string $name = null)
    {
        return $this->getDriver($name);
    }

    /**
     * 验证文件后缀
     * @access  protected
     * @param   string  $fileName   文件名称
     * @return  bool
     */
    protected function checkExt(string $fileName):bool
    {
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        return in_array($ext, $this->getConfig('allow_exts', []));
    }

    /**
     * 获取上传参数
     * @access  public
     * @param   string     $fileName   文件名称
     * @param   string     $fileMd5    文件Md5值
     * @return  array
     */
    public function info(string $fileName, string $fileMd5)
    {
        if (!$this->checkExt($fileName)) {
            $this->setError(Lang::get(Variable::FILE_TYPE_NOT_ALLOWED));
            return false;
        }
        $options = [
            'url' => $this->driver()->getAccessUrl($fileName, $fileMd5),
        ];
        if ($this->driver()->has($fileName, $fileMd5)) {
            $options['options'] = null;
            return $options;
        }
        $options['options'] = $this->driver()->info($fileName, $fileMd5);
        return $options;
    }

    /**
     * 获取切片上传参数
     * @access  public
     * @param   string     $fileName   文件名称
     * @param   string     $fileMd5    文件Md5值
     * @return  array
     */
    public function partInfo(string $fileName, string $fileMd5)
    {
        if (!$this->checkExt($fileName)) {
            $this->setError(Lang::get(Variable::FILE_TYPE_NOT_ALLOWED));
            return false;
        }
        $options = [
            'url' => $this->driver()->getAccessUrl($fileName, $fileMd5),
        ];
        if ($this->driver()->has($fileName, $fileMd5)) {
            $options['options'] = null;
            return $options;
        }
        $options['options'] = $this->driver()->partInfo($fileName, $fileMd5);
        return $options;
    }

    /**
     * 获取单个切片参数
     * @access  public
     * @param   string  $fileName   文件名称
     * @param   string  $fileMd5    文件md5
     * @param   string  $uploadId   上传ID
     * @param   int     $partNumber 标识
     * @return  array
     */
    public function partOptions(string $fileName, string $fileMd5, string $uploadId, int $partNumber)
    {
        $res = $this->driver()->partOptions($fileName, $fileMd5, $uploadId, $partNumber);
        if (!is_array($res)) {
            $this->setError($res);
            return false;
        }
        return $res;
    }

    /**
     * 完成切片上传
     * @param   string  $fileName   文件名称
     * @param   string  $fileMd5    文件md5
     * @param   string  $uploadId   上传ID
     * @param   array   $parts      切片信息列表
     * @return  array
     */
    public function partComplete(string $fileName, string $fileMd5, string $uploadId, array $parts)
    {
        $res = $this->driver()->partComplete($fileName, $fileMd5, $uploadId, $parts);
        if ($res !== true) {
            $this->setError($res);
            return false;
        }
        return [
            'url' => $this->driver()->getAccessUrl($fileName, $fileMd5),
        ];
    }
}