<?php

namespace traits\model;

/**
 * 工具方法
 */
trait Tools
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