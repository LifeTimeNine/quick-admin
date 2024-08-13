<?php

declare(strict_types = 1);

namespace provider;

use model\SystemErrorLog;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\View;
use think\Request;
use Throwable;
use think\Response;
use think\response\Html;

/**
 * 异常处理
 */
class ExceptionHandle extends Handle
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
            if ($e instanceof HttpException && $e->getStatusCode() == 404) {
                return Html::create()->code(404);
            }
            $hash = $this->buildHash($request, $e);
            $errorLog = SystemErrorLog::where('hash', $hash)->where('status', 1)->find();
            if (empty($errorLog)) {
                SystemErrorLog::create([
                    'hash' => $hash,
                    'app_name' => $this->app->get('http')->getName(),
                    'path_info' => $request->pathinfo(),
                    'access_ip' => $request->ip(),
                    'request_param' => $request->param() ?: [],
                    'header' => $request->header() ?: [],
                    'session' => $this->app->get('session')->all() ?: [],
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
            return Html::create(View::fetch(root_path('view/public') . 'exception.html'))->code(500);
        }
    }

    /**
     * 构建异常哈希值
     * @access  protected
     * @param   Request     $request    请求类
     * @param   \Throwable  $e          异常类
     * @return  string
     */
    protected function buildHash(Request $request, Throwable $e): string
    {
        $arr = [
            $this->app->get('http')->getName(),
            $request->pathinfo(),
            $e->__toString()
        ];

        return sha1(implode('@', $arr));
    }
}