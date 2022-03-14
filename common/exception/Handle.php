<?php

namespace exception;

use model\SystemErrorLog;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\View;
use Throwable;
use think\Response;
use think\response\Html;

/**
 * 异常处理
 */
class Handle extends \think\exception\Handle
{
    public function render($request, Throwable $e): Response
    {
        if (
            $this->app->isDebug() ||
            $e instanceof ValidateException ||
            $e instanceof HttpResponseException
        ) {
            return parent::render($request, $e);
        } else {
            $hash = $this->buildHash($e);
            $errorLog = SystemErrorLog::getByHash($hash);
            if (empty($errorLog)) {
                SystemErrorLog::create([
                    'hash' => $hash,
                    'app_name' => $this->app->get('http')->getName(),
                    'path_info' => $request->pathinfo(),
                    'access_ip' => $request->ip(),
                    'request_param' => $request->param(),
                    'request_time' => date('Y-m-d H:i:s', $request->time()),
                    'error_code' => $e->getCode(),
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'error_trace' => $e->getTraceAsString(),
                    'last_happen_time' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $errorLog->save([
                    'last_happen_time' => date('Y-m-d H:i:s'),
                    'happen_num' => ['inc', 1],
                ]);
            }

            View::assign(['hash' => $hash]);
            return Html::create(View::fetch(root_path('view/public') . 'exception.html'), 'html', 500);
        }
    }

    /**
     * 构建异常哈希值
     * @access  protected
     * @param   \Throwable  $e
     * @return  string
     */
    protected function buildHash(Throwable $e): string
    {
        $arr = [
            $this->app->get('http')->getName(),
            $this->app->get('request')->pathinfo(),
            $e->__toString()
        ];

        return sha1(implode('@', $arr));
    }
}