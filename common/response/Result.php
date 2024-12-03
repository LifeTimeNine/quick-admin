<?php

declare(strict_types = 1);

namespace response;

use think\facade\Lang;

/**
 * 响应结果
 */
class Result
{
    /**
     * 状态码
     * @var int
     */
    protected int $code;

    /**
     * 消息
     * @var string
     */
    protected string $message;

    /**
     * 数据
     * @var array
     */
    protected array $data;

    /**
     * 响应类
     * @access  public
     * @param   int|Code    $code       状态码
     * @param   string      $message    消息
     * @param   array       $data       数据
     */
    public function __construct(int|Code $code, ?string $message = null, array $data = [])
    {
        if ($code instanceof Code) {
            $this->code = $code->value;
            if (empty($message)) $message = $code->message();
        } else {
            $this->code = $code;
        }

        $this->message = Lang::get($message);
        $this->data = $data;
    }

    /**
     * 结果是否成功
     * @access  public
     * @return  bool
     */
    public function success()
    {
        return $this->code == Code::SUCCESS->value;
    }

    /**
     * 构造返回数据
     * @access  public
     * @return  array
     */
    public function build(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ];
    }

    public function __toString()
    {
        return json_encode($this->build(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 创建响应类
     * @access  public
     * @param   int|Code    $code       状态码
     * @param   string      $message    消息
     * @param   array       $data       数据
     * @return  Result
     */
    public static function create(int|Code $code, ?string $message = null, array $data = [])
    {
        return new static($code, $message, $data);
    }
}