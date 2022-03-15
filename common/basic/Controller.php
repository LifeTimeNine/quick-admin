<?php

namespace basic;

use service\Code;
use think\exception\HttpResponseException;
use think\Response;
use think\response\Json;

/**
 * 控制器基类
 */
abstract class Controller
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    
    /**
     * 当前请求示例
     * @var \think\Request
     */
    protected $request;
    /**
     * 分页参数
     * @var array
     */
    private $pageParam = [];

    /**
     * 构造方法
     * @access  public
     * @param   \think\App  $app
     */
    public function __construct(\think\App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
    }

    /**
     * 返回数据
     * @access  protected
     * @param   int|array   $code       异常码(自定义或从 Code 类中取)
     * @param   string      $message    消息
     * @param   array       $data       数据
     * @throws  \think\exception\HttpResponseException
     */
    protected function return($code, string $message = null, $data = null)
    {
        throw new HttpResponseException(Json::create(Code::buildMsg($code, $message, $data), 'json'));
    }

    /**
     * 返回成功消息
     * @access  protected
     * @throws  \think\exception\HttpResponseException
     */
    protected function success()
    {
        $this->return(Code::SUCCESS);
    }
    /**
     * 返回异常消息
     * @access  protected
     * @param   int|array   $code       异常码(自定义或从 Code 类中取)
     * @param   string      $message    消息
     * @param   array       $data       数据
     * @throws  \think\exception\HttpResponseException
     */
    protected function error($code = Code::ERROR, string $message = null, $data = null)
    {
        $this->return($code, $message, $data);
    }
    /**
     * URL 重定向
     * @access  protected
     * @param   string  $url    跳转地址
     * @param   int     $code   跳转状态码
     */
    protected function redirect(string $url, int $code = 302)
    {
        throw new HttpResponseException(Response::create($url, 'redirect', $code));
    }
    /**
     * 返回 Map 数据
     * @access  protected
     * @param   array   $data   Map数据
     * @throws  \think\exception\HttpResponseException
     */
    protected function returnMap(array $data)
    {
        $this->return(Code::SUCCESS, null, ['map' => $data]);
    }
    /**
     * 返回 List 数据
     * @access  protected
     * @param   array   $list   List数据
     * @throws  \think\exception\HttpResponseException
     */
    protected function returnList(array $data)
    {
        $this->return(Code::SUCCESS, null, ['list' => $data]);
    }
    /**
     * 获取分页参数
     * @access protected
     * @return array
     */
    protected function getPageParam()
    {
        if (empty($this->pageParam)) {
            $this->pageParam = [
                $this->request->get('page/d', 1),
                $this->request->get('limit/d', 10)
            ];
        }
        return $this->pageParam;
    }
    /**
     * 返回分页数据
     * @access  protected
     * @param   array   $total  总数
     * @param   array   $items  数据集
     * @throws  \think\exception\HttpResponseException
     */
    protected function returnPage(int $total, array $items)
    {
        $data = [
            'total' => $total,
            'items' => $items,
        ];
        if (!empty($this->pageParam)) {
            $data['page'] = $this->pageParam[0];
            $data['limit'] = $this->pageParam[1];
        }
        $this->return(Code::SUCCESS, null, $data);
    }
    /**
     * 通过模型返回分页数据
     * @access  protected
     * @param   \think\db\Query|\think\Model    $query          查询实例
     * @param   string|array                    $order          排序
     * @throws  \think\exception\HttpResponseException
     */
    protected function returnModelPage($query, $order = 'id desc')
    {
        $this->returnPage(
            $query->count(),
            $query->page(...$this->getPageParam())->order($order)->select()->toArray()
        );
    }

    /**
     * 验证数据
     * @access  protected
     * @param   array   $data       验证的数据
     * @param   string  $validate   验证器名称
     * @param   string  $scene     验证场景
     * @throws  \think\exception\HttpResponseException
     */
    protected function validate(array $data, string $validate, string $scene = null)
    {
        /** @var \think\Validate $valid */
        $valid = new $validate;
        if (!empty($scene)) $valid->scene($scene);
        if (!$valid->check($data)) {
            $this->error(Code::PARAM_ERROR, $valid->getError());
        }
    }
}