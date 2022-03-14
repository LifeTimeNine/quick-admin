<?php

namespace service;

use Firebase\JWT\JWT;
use traits\tools\Instance;

/**
 * Token相关业务
 */
class Token
{
    use Instance;

    /**
     * 当前应用
     * @var \think\App
     */
    protected $app;

    /**
     * 模块名称
     * @var string
     */
    protected $moduleName = '';

    /**
     * Token原始数据
     * @var object
     */
    protected $data;

    protected function __construct($modelName)
    {
        $this->app = app();
        $this->moduleName = $modelName;
    }

    /**
     * 获取配置
     * @access protected
     * @param   string  $name
     * @retrurn mixed
     */
    protected function config($name = '')
    {
        if (empty($name)) {
            return $this->app->config->get("token.{$this->moduleName}");
        } else {
            return $this->app->config->get("token.{$this->moduleName}.{$name}");
        }
    }

    /**
     * UID生成token
     * @access  public
     * @param   int     $uid
     * @return  string
     */
    public function uidBuild($uid)
    {
        $time = $this->app->request->time();
        $tokenData = [
            'iss' => $this->config('iss'),
            'aud' => $this->config('aud'),
            'iat' => $time,
            'nbf' => $time,
            'uid' => $uid,
        ];
        if (!empty($this->config('exp'))) $tokenData['exp'] =  $time + $this->config('exp');

        return JWT::encode($tokenData, $this->config('salt'), 'HS256');
    }

    /**
     * 数据集生成token
     * @access  public
     * @param   array   $data
     * @return  string
     */
    public function dataBuild($data)
    {
        $time = $this->app->request->time();
        $tokenData = [
            'iss' => $this->config('iss'),
            'aud' => $this->config('aud'),
            'iat' => $time,
            'nbf' => $time,
            'data' => $data,
        ];
        if (!empty($this->config('exp'))) $tokenData['exp'] =  $time + $this->config('exp');

        return JWT::encode($tokenData, $this->config('salt'), 'HS256');
    }

    /**
     * 解析 UID token
     * @access  public
     * @param   string  $token
     * @return  string
     */
    public function parseUid($token)
    {
        $this->data = JWT::decode($token, $this->config('salt'), ['HS256']);
        return $this->data->uid;
    }

    /**
     * 解析 数据 token
     * @access  public
     * @param   string  $token
     * @retrun  Object
     */
    public function parseData($token)
    {
        $this->data = JWT::decode($token, $this->config('salt'), ['HS256']);
        return $this->data->data;
    }

    /**
     * 获取解析到的原始数据
     * @access  public
     * @return  object
     */
    public function getData()
    {
        return $this->data;
    }
}
