<?php

declare(strict_types = 1);

namespace subscribe;

use basic\Subscribe;
use Throwable;

/**
 * 系统相关事件订阅
 */
class System extends Subscribe
{
    /**
     * 异常事件
     * @access  public
     * @param   Throwable   $t      异常类
     * @return  void
     */
    public function onException(Throwable $t)
    {
        
    }
}