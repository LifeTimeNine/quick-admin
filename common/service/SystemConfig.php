<?php

namespace service;

use model\SystemConfig as ModelSystemConfig;
use traits\tools\Instance;

/**
 * 系统配置服务
 */
class SystemConfig
{
    use Instance;

    /**
     * 缓存实例
     * @var \think\Cache
     */
    protected $cache;
    /**
     * 模型实例
     * @var \model\SystemConfig
     */
    protected $model;
    /**
     * 配置数据
     * @var array
     */
    protected $configData = [];
    /**
     * 缓存标识
     * @var string
     */
    protected $cacheKey = 'system_config_data';

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->cache = app('cache');
        $this->model = new ModelSystemConfig;
        if (!$this->cache->has($this->cacheKey)) {
            $this->configData = array_column($this->model->select()->toArray(), 'value', 'key');
            $this->cache->set($this->cacheKey, $this->configData);
        } else {
            $this->configData = $this->cache->get($this->cacheKey);
        }
    }

    /**
     * 获取配置
     * @access  public
     * @param   string          $key        键
     * @param   mixed           $default    默认值
     * @return  mixed
     */
    public function get($key = null, $default = null)
    {
        if (empty($key)) return $this->configData;
        return $this->configData[$key] ?? $default;
    }

    /**
     * 批量获取配置
     * @access  public
     * @param   array   $keys   键列表
     * @return  array
     */
    public function batchGet(array $keys)
    {
        $data = [];
        foreach($keys as $key) {
            $data[$key] = $this->configData[$key] ?? null;
        }
        return $data;
    }

    /**
     * 刷新配置
     * @access  public
     */
    public function refresh()
    {
        $this->configData = array_column($this->model->select()->toArray(), 'value', 'key');
        $this->cache->set($this->cacheKey, $this->configData);
    }
}