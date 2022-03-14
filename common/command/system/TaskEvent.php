<?php

namespace command\system;

use model\SystemTask;
use model\SystemTaskLog;
use swoole\event\Http;
use Swoole\Timer;
use Swoole\Coroutine\System;
/**
 * 系统任务事件
 */
class TaskEvent extends Http
{
    /**
     * 任务列表
     * @var array
     */
    protected static $taskList = [];

    /**
     * Swoole 服务对象
     * @var \Swoole\Http\Server
     */
    protected static $server;

    public static function onWorkerStart($server, int $workerId)
    {
        if (!$server->taskworker) {
            self::$server = $server;
            $taskList = SystemTask::where('type', SystemTask::TYPE_TIMING)
                ->where('status', 1)
                ->visible(['id'])
                ->withAttr([
                    'crontab_data' => function($value, $data) {
                        return self::parseCrontab($data['crontab']);
                    }
                ])->append(['crontab_data'])
                ->select()
                ->toArray();
            self::$taskList = array_column($taskList, 'crontab_data', 'id');
            $server->tick(1000, function() use($server) {
                $time = time();
                foreach(self::$taskList as $id => $crontabData) {
                    if (self::inTimeInner($crontabData, $time)) {
                        $server->task($id);
                    }
                }
            });
        }
    }

    public static function onWorkerExit($server, int $workerId)
    {
        Timer::clearAll();
    }

    public static function onTaskCoroutine($server, $task)
    {
        $taskData = SystemTask::find($task->data);
        if (empty($taskData)) return;

        $logData = [
            'stid' => $taskData->id,
            'pid' => $server->getWorkerPid(),
            'exec_time' => date('Y-m-d H:i:s')
        ];
        $beginTime = microtime(true);
        $taskData->save(['exec_status' => 2]);
        try {
            $res = System::exec(trim($taskData->command) . (trim($taskData->params) ? ' ' . trim($taskData->params) : ''));
            $logData['run_time'] = floor((microtime(true) - $beginTime) * 1000000) / 1000000;
            $logData['output'] = $res['output'];
            if ($res['code'] == 0) {
                $logData['result'] = 1;
            } else {
                $logData['result'] = 2;
            }
        } catch (\Throwable $th) {
            $logData['end_time'] = microtime(true);
            $logData['result'] = 2;
        }
        $taskData->save([
            'exec_status' => 1,
            'last_exec_time' => $logData['exec_time'],
            'last_exec_result' => $logData['result'],
            'exec_num' => ['inc', 1],
        ]);
        SystemTaskLog::create($logData);
    }

    public static function onRequest($request, $response)
    {
        $uri = $request->server['request_uri'] ?? '';
        $method = strtoupper($request->getMethod());
        if ($uri == '/status') {
            self::returnJson($response, [
                'task_num' => count(self::$taskList),
            ]);
            return;
        } elseif ($uri == '/task') {
            $data = json_decode($request->getContent(), true);
            if (json_last_error() > 0 || empty($data['id'])) {
                self::returnError($response, 'Error content');
                return;
            }

            switch($method) {
                case 'PUT':
                    $task = SystemTask::find($data['id']);
                    if (empty($task)) {
                        self::returnError($response, 'Error id');
                        return;
                    }
                    if ($task->type == SystemTask::TYPE_TIMING) {
                        self::$taskList[$task->id] = self::parseCrontab($task->crontab);
                    }
                    $response->end('');
                    return;
                case 'POST':
                    if (empty($data['crontab'])) {
                        self::returnError($response, 'Error content');
                        return;
                    }
                    self::$taskList[$data['id']] = self::parseCrontab($data['crontab']);
                    $response->end('');
                    return;
                case 'DELETE':
                    unset(self::$taskList[$data['id']]);
                    $response->end('');
                    return;
                default:
                self::returnError($response, 'Error method');
                return;
            }
        } elseif ($request->server['request_uri'] == '/exec') {
            $data = json_decode($request->getContent(), true);
            if (json_last_error() > 0) {
                self::returnError($response, 'Error content');
                return;
            }
            if ($method == 'POST') {
                if (empty($data['id'])) {
                    self::returnError($response, 'Error content');
                    return;
                }
                self::$server->task($data['id']);
                $response->end('');
                return;
            } else {
                self::returnError($response, 'Error method');
            }
        } else {
            self::returnError($response, 'Error uri');
        }
    }

    /**
     * 解析定时任务参数
     * @access  private
     * @param   string  $crontab 定时任务参数
     * @return  array
     */
    private static function parseCrontab(string $crontab)
    {
        $crontabArr = array_pad(preg_split("/(\s+)/", trim($crontab)), 6, '*');
        return [
            'second' => self::parseCrontabItem($crontabArr[0], 0, 59),
            'minute' => self::parseCrontabItem($crontabArr[1], 0, 59),
            'hours' => self::parseCrontabItem($crontabArr[2], 0, 23),
            'day' => self::parseCrontabItem($crontabArr[3], 1, 31),
            'month' => self::parseCrontabItem($crontabArr[4], 1, 12),
            'week' => self::parseCrontabItem($crontabArr[5], 0, 6),
        ];
    }

    /**
     * 解析定时任务某个参数
     * @access  private
     * @param   string  $param  参数
     * @param   int     $min    最小值
     * @param   int     $max    最大值
     * @return  array
     */
    private static function parseCrontabItem(string $param, int $min, int $max)
    {
        $result = [];
        $list = explode(',', $param);
        foreach($list as $item) {
            $stepList = explode('/', $item);
            $step = empty($stepList[1]) ? 1 : $stepList[1];
            $scopeList = explode('-', $stepList[0]);
            $scopeMin = count($scopeList) == 2 ? $scopeList[0] : ($scopeList[0] == '*' ? $min : $scopeList[0]);
            $scopeMax = count($scopeList) == 2 ? $scopeList[1] : ($scopeList[0] == '*' ? $max : $scopeList[0]);
            $result = array_merge($result, range($scopeMin, $scopeMax, $step));
        }
        sort($result);
        return $result;
    }

    /**
     * 判断是否在时间内
     * @access  private
     * @param   array   $crontabData    定时任务参数
     * @param   int     $time           当前时间戳
     * @return bool
     */
    private static function inTimeInner(array $crontabData, int $time)
    {
        return in_array(date('s', $time), $crontabData['second']) &&
            in_array(date('i', $time), $crontabData['minute']) &&
            in_array(date('H', $time), $crontabData['hours']) &&
            in_array(date('d', $time), $crontabData['day']) &&
            in_array(date('m', $time), $crontabData['month']) &&
            in_array(date('w', $time), $crontabData['week']);
    }

    /**
     * 返回JSON数据
     * @access  private
     * @param   \Swoole\Http\Response   $response   响应对象
     * @param   array                   $data       返回数据
     */
    private static function returnJson($response, $data)
    {
        $response->header('content-type', 'text/json');
        $response->end(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    /**
     * 返回Error数据
     * @access  private
     * @param   \Swoole\Http\Response   $response   响应对象
     * @param   string                  $string     消息
     */
    private static function returnError($response, $message)
    {
        $response->status(500, $message);
        $response->end($message);
    }
}