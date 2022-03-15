<?php

namespace traits\tools;

trait Error
{
    /**
     * 异常信息
     */
    private $error;

    /**
     * 设置异常信息
     * @access  protected
     * @param   mixed   $error  异常信息
     */
    protected function setError($error)
    {
        $this->error = $error;
    }

    /**
     * 获取异常信息
     * @access  public
     * @return  mixed
     */
    public function getError()
    {
        return $this->error;
    }
}