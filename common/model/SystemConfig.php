<?php

namespace model;

use basic\Model;

/**
 * 系统配置模型
 */
class SystemConfig extends Model
{
    protected $pk = 'id';
    protected $table = 'system_config';

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
}