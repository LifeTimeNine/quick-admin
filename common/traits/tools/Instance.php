<?php

namespace traits\tools;

/**
 * 静态实例化
 */
trait Instance
{
    /**
     * 实例化列表
     * @var array
     */
    protected static $instanceList = [];

    /**
     * 静态初始化 单例
     * @access  public
     * @return  $this
     */
    public static function instance()
    {
        $key = hash('sha256', get_called_class() . serialize(func_get_args()));
        if (!isset(self::$instanceList[$key])) {
            self::$instanceList[$key] = new static(...func_get_args());
        }
        return self::$instanceList[$key];
    }

    /**
     * 静态示例
     * @access  public
     * @return  $this
     */
    public static function make()
    {
        return new static(...func_get_args());
    }
}