<?php

declare(strict_types = 1);

namespace service;

use think\facade\Config;
use tools\Request;
use traits\tools\Error;
use traits\tools\Instance;

/**
 * 定时任务服务
 */
class Timer
{
    use Instance, Error;

    /**
     * 配置
     * @var array
     */
    protected $config = [
        'host' => '127.0.0.1',
        'port' => 10010
    ];

    /**
     * 构造函数
     * @access  public
     * @param   array   $config     配置
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, Config::get('timer', []), $config);
    }

    /**
     * 发起请求
     * @access  protected
     * @param   string      $method     请求方法
     * @param   string      $uri        请求地址
     * @param   array       $body       请求数据
     * @param   int         $timeout    超时时间(毫秒)
     * @return  mixed
     */
    protected function request(string $method, string $uri, array $body = [], int $timeout = 1000)
    {
        dump("http://{$this->config['host']}:{$this->config['port']}{$uri}");
        $request = new Request("http://{$this->config['host']}:{$this->config['port']}{$uri}", $method);
        $request->setOptions(CURLOPT_CONNECTTIMEOUT_MS, $timeout)
            ->setHeaders([
                'content-type' => 'application/json'
            ])
            ->setBody(json_encode($body));
        $result = $request->send();
        dump($result, $request->getCode());
        if ($request->getCode() <> 200) {
            $this->setError('请求失败');
            return false;
        }
        $result = json_decode($result, true);
        if (json_last_error() > 0) {
            $this->setError('结果解析失败');
            return false;
        }
        if ($result['status'] <> 0) {
            $this->setError($result['message']);
            return false;
        }
        return $result['data'];
    }

    /**
     * 获取服务状态
     * @access  public
     * @return  array|false
     */
    public function state()
    {
        return $this->request(Request::METHOD_GET, '/');
    }
}