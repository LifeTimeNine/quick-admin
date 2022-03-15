<?php

namespace service;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use traits\tools\Error;
use traits\tools\Instance;

/**
 * Token 相关服务
 */
class Token
{
    use Instance,Error;

    /**
     * 当前应用
     * @var \think\App
     */
    protected $app;

    /**
     * 应用名称
     * @var string
     */
    protected $appName;

    /**
     * 请求域名
     * @var string
     */
    protected $domain;

    /**
     * Token 数据
     * @var array
     */
    protected $data;

    public function __construct()
    {
        $this->app = app();
        $this->appName = $this->app->http->getName();
        $this->domain = $this->app->request->domain();
    }

    /**
     * 获取配置
     * @access protected
     * @param   string  $name
     * @retrurn mixed
     */
    protected function config($name = null)
    {
        if (empty($name)) {
            return $this->app->config->get("token");
        } else {
            return $this->app->config->get("token.{$name}");
        }
    }
    /**
     * 获取应用配置
     * @access  protected
     * @param   string  $name
     * @return  mixed
     */
    protected function appConfig($name = null)
    {
        if (empty($name)) {
            return $this->config("apps.{$this->appName}");
        } else {
            return $this->config("apps.{$this->appName}.{$name}");
        }
    }

    /**
     * 构建Token
     * @access  public
     * @param   mixed   $data       额外数据
     * @param   string  $sub        主题
     * @return  array
     */
    public function build($data, string $sub = null)
    {
        $time = $this->app->request->time();
        $jti = sha1("{$this->domain}{$this->appName}{$sub}" . serialize($data));
        $payload = [
            'iss' => $this->domain,
            'sub' => $sub,
            'aud' => $this->appName,
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + $this->appConfig('expire', $this->config('default_expire')),
            'jti' => $jti,
            'data' => $data
        ];
        $refreshPaylod = [
            'iss' => $this->domain,
            'sub' => $sub,
            'aud' => $this->appName,
            'iat' => $time,
            'nbf' => $time,
            'jti' => $jti,
            'data' => $data
        ];
        $this->setJtiTime($jti, $time);
        return [
            'access_token' => JWT::encode($payload, $this->config('salt')),
            'expire' => $payload['exp'],
            'refresh_token' => JWT::encode($refreshPaylod, $this->config('refresh_salt'))
        ];
    }
    /**
     * 刷新Token
     * @access  public
     * @param   string      $token      Token
     * @param   string      $sub        主题
     * @param   callable    $callable   自定义验证方法
     * @return  array
     */
    public function refresh(string $token, string $sub, callable $callable = null)
    {
        try {
            $this->data = json_decode(json_encode(JWT::decode($token, $this->config('refresh_salt'), ['HS256'])), true);
        } catch (\Throwable $th) { //其他异常
            $this->setError(Code::TOKEN_REFRESH_FAIL);
            return false;
        }
        if (
            $this->data['iss'] <> $this->domain ||
            $this->data['sub'] <> $sub ||
            $this->data['aud'] <> $this->appName
        ) {
            $this->setError(Code::TOKEN_REFRESH_FAIL);
            return false;
        }
        if (is_callable($callable)) {
            $res = call_user_func($callable, $this->data);
            if ($res !== true) {
                $this->setError($res);
                return false;
            }
        }
        $time = $this->app->request->time();
        $payload = [
            'iss' => $this->domain,
            'sub' => $sub,
            'aud' => $this->appName,
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + $this->appConfig('expire', $this->config('default_expire')),
            'jti' => $this->data['jti'],
            'data' => $this->data['data']
        ];
        return [
            'access_token' => JWT::encode($payload, $this->config('salt')),
            'expire' => $payload['exp'],
        ];
    }
    /**
     * 解析Token
     * @access  public
     * @param   string  $token  Token
     * @param   string  $sub    主题
     * @return  mixed
     */
    public function parse(string $token, string $sub = null)
    {
        try {
            $this->data = json_decode(json_encode(JWT::decode($token, $this->config('salt'), ['HS256'])), true);
        } catch(ExpiredException $e) { // token过期
            $this->setError(Code::TOKEN_EXPIRE);
            return false;
        } catch (\Throwable $th) { //其他异常
            $this->setError(Code::TOKEN_ERROR);
            return false;
        }
        /// 验证 信息
        if (
            $this->data['iss'] <> $this->domain ||
            $this->data['sub'] <> $sub ||
            $this->data['aud'] <> $this->appName
        ) {
            $this->setError(Code::TOKEN_ERROR);
            return false;
        }
        // 验证有效性
        if ($this->data['iat'] < $this->getJtiTime($this->data['jti'])) {
            $this->setError(Code::TOKEN_FIALURE);
            return false;
        }
        return $this->data['data'];
    }
    /**
     * 获取Token所有数据
     * @access  public
     * @return  array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * 设置jti时间
     * @access  protected
     * @param   string  $jti
     * @param   int     $time
     */
    protected function setJtiTime(string $jti, int $time)
    {
        $this->app->cache->set("jti_{$jti}", $time);
    }
    /**
     * 获取jti时间
     * @access  protected
     * @return  int
     */
    protected function getJtiTime(string $jti)
    {
        return $this->app->cache->get("jti_{$jti}");
    }
}