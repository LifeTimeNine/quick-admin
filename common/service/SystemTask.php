<?php

namespace service;

use think\facade\Config;
use tools\Request;
use traits\tools\Instance;

/**
 * 系统任务服务
 */
class SystemTask
{
    use Instance;

    /**
     * 配置
     * @var \swoole\Config
     */
    protected $config;

    /**
     * 获取配置
     * @access  public
     * @return  \swoole\Config
     */
    public function getServerConfig()
    {
        if (!empty($this->config)) return $this->config;
        $serverConfig = Config::get('system_task.server', []);
        $this->config = new \swoole\Config();
        $this->config->setPort($serverConfig['port'] ?? 9501)
            ->setWorkerNum(1)
            ->setTaskWorkerNum($serverConfig['task_worker_num'] ?? 5)
            ->setTaskEnableCoroutine(true)
            ->setUser('www')
            ->setGroup('www');
        if (empty($serverConfig['pid_file'])) {
            $serverConfig['pid_file'] = runtime_path('system_task') . '.pid';
        }
        $pidFileDir = pathinfo($serverConfig['pid_file'], PATHINFO_DIRNAME);
        if (!is_dir($pidFileDir)) mkdir($pidFileDir, 0777, true);
        $this->config->setPidFile($serverConfig['pid_file']);
        return $this->config;
    }

    /**
     * 获取服务是否运行
     * @access  public
     * @return  bool
     */
    public function isRunning()
    {
        $request = new Request("http://127.0.0.1:{$this->getServerConfig()->getPort()}/status");
        $request->setOptions(CURLOPT_CONNECTTIMEOUT_MS, 500);
        $request->send();
        return $request->getCode() == 200;
    }

    /**
     * 添加任务
     * @access  public
     * @param   int     $id         任务ID
     * @return  bool
     */
    public function append(int $id)
    {
        $request = new Request("http://127.0.0.1:{$this->getServerConfig()->getPort()}/task", Request::METHOD_PUT);
        $request->setOptions(CURLOPT_CONNECTTIMEOUT_MS, 500)
            ->setHeaders([
                'content-type' => 'application/json'
            ])
            ->setBody(json_encode([
                'id' => $id
            ]));
        $request->send();
        return $request->getCode() == 0 || $request->getCode() == 200;
    }
    /**
     * 设置任务
     * @access  public
     * @param   int     $id         任务ID
     * @param   string  $crontab    定时参数
     * @return  bool
     */
    public function set(int $id, string $crontab)
    {
        $request = new Request("http://127.0.0.1:{$this->getServerConfig()->getPort()}/task", Request::METHOD_POST);
        $request->setOptions(CURLOPT_CONNECTTIMEOUT_MS, 500)
            ->setHeaders([
                'content-type' => 'application/json'
            ])
            ->setBody(json_encode([
                'id' => $id,
                'crontab' => $crontab
            ]));
        $request->send();
        return $request->getCode() == 0 || $request->getCode() == 200;
    }
    /**
     * 删除任务
     * @access  public
     * @param   int     $id         任务ID
     * @return  bool
     */
    public function remove(int $id)
    {
        $request = new Request("http://127.0.0.1:{$this->getServerConfig()->getPort()}/task", Request::METHOD_DELETE);
        $request->setOptions(CURLOPT_CONNECTTIMEOUT_MS, 500)
            ->setHeaders([
                'content-type' => 'application/json'
            ])
            ->setBody(json_encode([
                'id' => $id,
            ]));
        $request->send();
        return $request->getCode() == 0 || $request->getCode() == 200;
    }
    /**
     * 执行任务
     * @access  public
     * @param   int     $id         任务ID
     * @return  bool
     */
    public function exec(int $id)
    {
        $request = new Request("http://127.0.0.1:{$this->getServerConfig()->getPort()}/exec", Request::METHOD_POST);
        $request->setOptions(CURLOPT_CONNECTTIMEOUT_MS, 500)
            ->setHeaders([
                'content-type' => 'application/json'
            ])
            ->setBody(json_encode([
                'id' => $id,
            ]));
        $request->send();
        return $request->getCode() == 200;
    }
}