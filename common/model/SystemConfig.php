<?php

namespace model;

use basic\Model;
use think\facade\Cache;

/**
 * 系统配置模型
 */
class SystemConfig extends Model
{
    protected $pk = 'id';
    protected $table = 'system_config';
    protected $autoWriteTimestamp = false;

    protected static $cachePrefix = 'system_config_';

    /**
     * 类型 文本
     */
    const TYPE_TEXT = 1;
    /**
     * 类型 列表
     */
    const TYPE_LIST = 2;
    /**
     * 类型 Map表
     */
    const TYPE_MAP = 3;

    /** 键 系统名称 */
    const KEY_SYSTEM_NAME = 'system_name';

    /**
     * 值 修改器
     */
    public function setValueAttr($value, $data)
    {
        if (in_array($data['type'], [self::TYPE_LIST, self::TYPE_MAP])) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            return $value;
        }
    }

    /**
     * 值 获取器
     */
    public function getValueAttr($value, $data)
    {
        if (in_array($data['type'], [self::TYPE_LIST, self::TYPE_MAP])) {
            return json_decode($value, true);
        } else {
            return $value;
        }
    }

    /**
     * 获取配置
     * @access  public
     * @param   string          $key        键
     * @param   mixed           $default    默认值
     * @return  mixed
     */
    public static function get($key = null, $default = null): mixed
    {
        $cacheKey = self::$cachePrefix . $key;
        if (!Cache::has($cacheKey)) {
            $value = static::where('key', $key)->value('value') ?: $default;
            Cache::set($cacheKey, $value);
        } else {
            $value = Cache::get($cacheKey, $default);
        }
        return $value;
    }

    /**
     * 批量获取配置
     * @access  public
     * @param   array   $keys   键列表
     * @return  array
     */
    public static function batchGet(array $keys): array
    {
        $result = [];
        foreach($keys as $key) {
            $cacheKey = self::$cachePrefix . $key;
            if (Cache::get($cacheKey)) {
                $result[$key] = Cache::get($cacheKey);
            }
        }
        $notExistKeyList = array_diff($keys, array_keys($result));
        $list = static::whereIn('key', $notExistKeyList)->column('value', 'key');
        foreach($notExistKeyList as $key) {
            $result[$key] = $list[$key] ?: null;
            Cache::set(self::$cachePrefix . $key, $result[$key]);
        }
        return $result;
    }

    /**
     * 刷新缓存
     * @access  public
     * @param   string  $key    键
     * @return  void
     */
    public static function refreshCache(string $key): void
    {
        $value = static::where('key', $key)->value('value') ?: null;
        Cache::set(self::$cachePrefix . $key, $value);
    }

}
