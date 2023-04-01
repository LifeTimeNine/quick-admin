<?php

namespace service;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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

    /**
     * 加密盐
     * @var string
     */
    protected $salt = 'QuickAdmin';

    /**
     * 配置
     * @var array
     */
    protected $config = [
        //  Token有效时间
        'expire' => 3600 * 24 * 3,
        // 是否自动刷新token
        'auto_refresh' => true,
        // 自动刷新token的剩余时间占比
        'auto_refresh_time_ratio' => 0.1,
    ];

    public function __construct()
    {
        $this->app = app();
        $this->appName = $this->app->http->getName();
        $this->domain = $this->app->request->domain();
        $this->salt = $this->app->config->get('token.salt', $this->salt);
        $this->config = array_merge($this->config, $this->app->config->get("token.apps.{$this->appName}", []));
    }

    /**
     * 获取配置
     * @access protected
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    protected function config(string $name = null, $default = null)
    {
        if (empty($name)) {
            return $this->config;
        } else {
            return $this->config[$name] ?? $default;
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
        $payload = [
            'iss' => $this->domain,
            'sub' => $sub,
            'aud' => $this->appName,
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + $this->config('expire'),
            'jti' => sha1("{$this->domain}{$this->appName}{$sub}" . serialize($data) . time()),
            'data' => $data
        ];
        $this->app->cache->set("jti_{$payload['jti']}", ['has_refresh' => false], $this->config('expire'));
        return JWT::encode($payload, $this->salt, 'HS256');
    }
    /**
     * 刷新Token
     * @access  protected
     * @return  array
     */
    protected function refresh()
    {
        $time = $this->app->request->time();
        $this->app->cache->set("jti_{$this->data['jti']}", ['has_refresh' => true], 300);
        $this->data['exp'] = $time + $this->config('expire');
        $this->data['jti'] = sha1("{$this->domain}{$this->appName}{$this->data['sub']}" . serialize($this->data['data']) . time());
        $this->app->cache->set("jti_{$this->data['jti']}", ['has_refresh' => false], $this->config('expire'));
        $this->refreshToken = JWT::encode($this->data, $this->salt, 'HS256');
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
            $this->data = json_decode(json_encode(JWT::decode($token, new Key($this->salt, 'HS256'))), true);
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
        if (empty($jtiData = $this->app->cache->get("jti_{$this->data['jti']}"))) {
            $this->setError(Code::TOKEN_FAILURE);
            return false;
        }
        // 判断有效期
        if ($this->config('auto_refresh') && !$jtiData['has_refresh'] && ($this->data['exp'] - time()) < $this->config('expire') * $this->config('auto_refresh_time_ratio')) {
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
        $this->app->cache->delete("jti_{$this->data['jti']}");
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
}