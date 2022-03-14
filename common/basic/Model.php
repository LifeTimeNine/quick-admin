<?php

namespace basic;

/**
 * 模型基类
 * @package basic
 */
abstract class Model extends \think\Model
{
    /**
     * 获取数据表名
     * @access public
     * @return string
     */
    public static function getTableName(): string
    {
        $db = (new static)->db();
        return $db->getConfig('database') . '.' . $db->getTable();
    }
}