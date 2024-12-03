<?php

declare(strict_types = 1);

namespace attribute;

use Attribute;

/**
 * 控制器注解类
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Controller
{
    /**
     * 标题
     * @var string
     */
    private string $title;

    /**
     * 控制器注解类
     * @access  public
     * @param   string  $title  标题
     */
    public function __construct(string $title)
    {
        $this->title = $title;
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
}