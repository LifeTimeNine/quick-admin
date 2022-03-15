<?php

namespace traits\model;

/**
 * 状态查询
 */
trait ScopeStatus
{
    /**
     * 查询启用数据
     */
    public function scopeEnable($query)
    {
        $query->where('status', 1);
    }
    /**
     * 查询禁用数据
     */
    public function scopeDisable($query)
    {
        $query->where('status', 2);
    }
}