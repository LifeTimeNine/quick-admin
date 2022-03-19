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

    /**
     * 刷新token
     * @var string
     */
    protected $refreshToken;

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
     * @param   mixed   $default
     * @retrurn mixed
     */
    protected function config(string $name = null, $default = null)
    {
        if (empty($name)) {
            return $this->app->config->get("token", $default);
        } else {
            return $this->app->config->get("token.{$name}", $default);
        }
    }
    /**
     * 获取应用配置
     * @access  protected
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    protected function appConfig(string $name = null, $default = null)
    {
        if (empty($name)) {
            return $this->config("apps.{$this->appName}", $default);
        } else {
            return $this->config("apps.{$this->appName}.{$name}", $default);
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
            'exp' => $time + $this->appConfig('expire', $this->config('default_expire', 3600 * 6)),
            'jti' => $jti,
            'data' => $data
        ];
        $this->setJtiTime($jti, $time, $payload['exp']);
        return JWT::encode($payload, $this->config('salt'));
    }
    /**
     * 刷新Token
     * @access  protected
     * @return  array
     */
    protected function refresh()
    {
        $time = $this->app->request->time();
        $this->data['exp'] = $time + $this->appConfig('expire', $this->config('default_expire', 3600 * 6));
        $this->setJtiTime($this->data['jti'], $time, $this->data['exp']);
        $this->refreshToken = JWT::encode($this->data, $this->config('salt'));
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
        if ($this->data['iat'] <> $this->getJtiTime($this->data['jti'])) {
            $this->setError(Code::TOKEN_FIALURE);
            return false;
        }
        // 判断有效期
        if ($this->config('auto_refresh') && ($this->data['exp'] - time()) < $this->appConfig('expire', $this->config('default_expire')) * $this->config('auto_refresh_time_ratio', 0.1)) {
            $this->refresh();
        }
        return $this->data['data'];
    }
    /**
     * 退出登录
     * @access  public
     */
    public function logout()
    {
        $this->clearJti($this->data['jti']);
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
     * 获取刷新token
     * @access  public
     * @return  string|null
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * 设置jti时间
     * @access  protected
     * @param   string  $jti
     * @param   int     $time
     */
    protected function setJtiTime(string $jti, int $time)
    {
        $this->app->cache->set("jti_{$jti}", $time, $this->appConfig('expire', $this->config('default_expire')));
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
    /**
     * 清除jti
     * @access  protected
     * @param   string  $jti
     */
    protected function clearJti(string $jti)
    {
        return $this->app->cache->delete("jti_{$jti}");
    }
}