<?php

declare(strict_types = 1);

namespace attribute;

use Attribute;

/**
 * 操作注解类
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Action
{
    /**
     * 标题
     * @var string
     */
    private string $title;

    /**
     * 权限验证
     * @var bool
     */
    private bool $auth;

    /**
     * 菜单显示
     * @var bool
     */
    private bool $menu;

    /**
     * 记录日志
     * @var bool
     */
    private bool $log;

    /**
     * 节点注解类
     * @access  public
     * @param   string  $title  标题
     * @param   bool    $auth   权限验证
     * @param   bool    $menu   菜单显示
     * @param   bool    $log    记录日志
     */
    public function __construct(string $title, bool $auth = false, bool $menu = false,bool $log = false)
    {
        $this->title = $title;
        $this->auth = $auth;
        $this->menu = $menu;
        $this->log = $log;
    }

    /**
     * 标题
     * @access  public
     * @return  string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * 权限验证
     * @access  public
     * @return  bool
     */
    public function getAuth(): bool
    {
        return $this->auth;
    }

    /**
     * 菜单显示
     * @access  public
     * @return  bool
     */
    public function getMenu(): bool
    {
        return $this->menu;
    }

    /**
     * 日志记录
     * @access  public
     * @return  bool
     */
    public function getLog(): bool
    {
        return $this->log;
    }
}
