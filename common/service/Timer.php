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
     * @param   array       $query      请求参数
     * @param   array       $body       请求数据
     * @param   int         $timeout    超时时间(毫秒)
     * @return  mixed
     */
    protected function request(string $method, string $uri, array $query = [], array $body = [], int $timeout = 1000)
    {
        $url = "http://{$this->config['host']}:{$this->config['port']}{$uri}";
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        $request = new Request($url, $method);
        $request->setOptions(CURLOPT_CONNECTTIMEOUT_MS, $timeout)
            ->setHeaders([
                'content-type' => 'application/json'
            ])
            ->setBody(json_encode($body));
        $result = $request->send();
        if ($request->getCode() <> 200) {
            $this->setError('请求失败');
            return false;
        }
        $result = json_decode($result, true);
        if (json_last_error() > 0) {
            $this->setError('结果解析失败');
            return false;
        }
        return $result;
    }

    /**
     * 获取服务状态
     * @access  public
     * @return  array|false
     */
    public function state()
    {
        return $this->request(Request::METHOD_GET, '/', [], [], 500);
    }

    /**
     * 保存任务
     * @access  public
     * @param   string  $uuid       唯一编号
     * @param   string  $execFile   可执行文件路径
     * @param   string  $args       参数
     * @param   bool    $loop       是否循环
     * @param   bool    $enable     是否启用
     * @param   string  $cron       Cron表达式
     * @return  array|false
     */
    public function taskSave(string $uuid, string $execFile, string $args, bool $loop, bool $enable, string $cron)
    {
        return $this->request(Request::METHOD_POST, '/task', [
            'uuid' => $uuid
        ], [
            'exec_file' => $execFile,
            'args' => $args,
            'loop' => $loop,
            'enable' => $enable,
            'cron' => $cron
        ]);
    }

    /**
     * 获取任务列表
     * @access  public
     * @return  array|false
     */
    public function taskList()
    {
        return $this->request(Request::METHOD_GET, '/task');
    }

    /**
     * 获取任务详情
     * @access  public
     * @param   string  $uuid   任务UUID
     * @return  array|false
     */
    public function taskDetail(string $uuid)
    {
        return $this->request(Request::METHOD_GET, "/task", ['uuid' => $uuid]);
    }

    /**
     * 删除任务
     * @access  public
     * @param   string  $uuid   任务UUID
     * @return  array|false
     */
    public function taskRemove(string $uuid)
    {
        return $this->request(Request::METHOD_DELETE, "/task", ['uuid' => $uuid]);
    }

    /**
     * 设置任务状态
     * @access  public
     * @param   string  $uuid   任务UUID
     * @param   bool    $enable 是否启用
     * @return  array|false
     */
    public function taskStatus(string $uuid, bool $enable)
    {
        return $this->request(Request::METHOD_POST, '/task/status', ['uuid' => $uuid], ['enable' => $enable]);
    }

    /**
     * 运行任务
     * @access  public
     * @param   string  $uuid   任务UUID
     * @return  array|false
     */
    public function taskRun(string $uuid)
    {
        return $this->request(Request::METHOD_POST, '/task/run', ['uuid' => $uuid]);
    }
}