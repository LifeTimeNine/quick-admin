<?php

namespace tools;

use Exception;

/**
 * 网络请求类
 * @class   Request
 */
class Request
{
    /**
     * GET 请求
     */
    const METHOD_GET = 'GET';
    /**
     * POST 请求
     */
    const METHOD_POST = 'POST';
    /**
     * PUT 请求
     */
    const METHOD_PUT = 'PUT';
    /**
     * DELETE 请求
     */
    const METHOD_DELETE = 'DELETE';
    /**
     * HEAD 请求
     */
    const METHOD_HEAD = 'HEAD';


    /**
     * curl 参数
     * @var array
     */
    protected $options = [];

    /**
     * 响应信息
     * @var array
     */
    protected $info;

    /**
     * 响应头
     * @var array
     */
    protected $headers = [];

    /**
     * 构造函数
     * @param   string  $url    请求地址
     * @param   string  $method 请求方法
     */
    public function __construct(string $url, string $method = Request::METHOD_GET)
    {
        switch ($method) {
            case self::METHOD_GET:
            case self::METHOD_POST:
            case self::METHOD_PUT:
            case self::METHOD_DELETE:
                break;
            case self::METHOD_HEAD:
                $this->setOptions([
                    CURLOPT_NOBODY => true,
                    CURLINFO_HEADER_OUT => true,
                ]);
                break;
            default:
                throw new Exception("unsupported request method: {$method}");
        }
        $this->setOptions([
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method
        ]);
        if (parse_url($url, PHP_URL_SCHEME) == 'https') {
            $this->setOptions([
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
        }
    }

    /**
     * 设置选项
     * @param   int|array   $option
     * @param   mixed       $value
     * @return  $this
     */
    public function setOptions($option, $value = null)
    {
        if (is_array($option)) {
            foreach($option as $k => $v) $this->options[$k] = $v;
        } else {
            $this->options[$option] = $value;
        }
        return $this;
    }

    /**
     * 设置请求头
     * @param   array    $headers
     * @return  $this
     */
    public function setHeaders($headers)
    {
        $this->setOptions(CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    /**
     * 设置请求数据
     * @param   mixed   $data
     * @return  $this
     */
    public function setBody($data)
    {
        $this->setOptions(CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    /**
     * 发送请求
     * @return  string
     */
    public function send()
    {
        $this->setOptions([
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, $this->options);
        $content = curl_exec($curl);
        $this->info = curl_getinfo($curl);
        $this->formatHeaders(substr($content, 0, $this->info['header_size']));
        return substr($content, $this->info['header_size']);
    }

    /**
     * 获取相应信息
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getInfo(string $key = null, $default = null)
    {
        return is_null($key) ? $this->info : ($this->info[$key] ?? $default);
    }

    /**
     * 获取相应状态码
     * @return  int
     */
    public function getCode()
    {
        return $this->getInfo('http_code');
    }

    /**
     * 整理响应头
     * @param   string  $headerStr
     */
    protected function formatHeaders($headerStr)
    {
        $headerArr = array_diff(explode("\r\n", $headerStr), [""]);
        array_shift($headerArr);
        array_map(function($value) {
            if (strpos($value, ': ') === false) return;
            [$k, $v] = explode(': ', $value);
            $this->header[$k] = $v;
        }, $headerArr);
    }

    /**
     * 获取响应头
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getHeader(string $key = null, $default = null)
    {
        return is_null($key) ? $this->header : ($this->header[$key] ?? $default);
    }

    /**
     * GET 请求
     * @param   string  $url    请求地址
     * @return  $this
     */
    public static function get(string $url)
    {
        return new static($url, self::METHOD_GET);
    }
    /**
     * POST 请求
     * @param   string  $url    请求地址
     * @return  $this
     */
    public static function post(string $url)
    {
        return new static($url, self::METHOD_POST);
    }
    /**
     * PUT 请求
     * @param   string  $url    请求地址
     * @return  $this
     */
    public static function put(string $url)
    {
        return new static($url, self::METHOD_PUT);
    }
    /**
     * DELETE 请求
     * @param   string  $url    请求地址
     * @return  $this
     */
    public static function delete(string $url)
    {
        return new static($url, self::METHOD_DELETE);
    }
    /**
     * HEAD 请求
     * @param   string  $url    请求地址
     * @return  $this
     */
    public static function head(string $url)
    {
        return new static($url, self::METHOD_HEAD);
    }
}