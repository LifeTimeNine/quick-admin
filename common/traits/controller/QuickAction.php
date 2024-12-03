<?php

namespace traits\controller;

use basic\Model as BasicModel;
use response\Code;
use think\db\Query;
use think\db\Where;
use think\Exception;
use think\facade\Db;
use think\facade\Validate as FacadeValidate;
use think\Model;
use think\Validate;

/**
 * 控制器快捷操作
 */
trait QuickAction
{
    /**
     * 当前应用信息
     * @var \think\App
     */
    protected $app;
    /**
     * 当前请求信息
     * @var \think\Request
     */
    protected $request;

    /**
     * 数据回调处理
     * @access  private
     * @param   callable    $callable       闭包函数
     * @param   string      $methodSuffix   成员方法后缀
     * @param   array       $params         参数
     * @return  mixed
     */
    private function _callback(callable $callable = null, string $methodSuffix = null, array $params = [])
    {
        if (is_callable($callable)) {
            return call_user_func_array($callable, $params);
        } else {
            foreach(["_{$this->request->action()}_{$methodSuffix}"] as $method) {
                if (method_exists($this, $method)) {
                    return call_user_func_array([$this, $method], $params);
                }
            }
            return true;
        }
    }

    /**
     * 快捷分页
     * @access  protected
     * @param   string|Query|Model  $model      操作模型
     * @param   array|Where         $condition  查询条件
     * @param   string|array        $order      排序 (默认 id desc)
     * @param   callable            $filter     过滤器
     * @throws  HttpResponseException
     */
    protected function _page(string|Query|Model $model, array|Where $condition = [], string|array $order = null, callable $filter = null)
    {
        if (!empty($condition)) {
            if (is_array($condition)) {
            } elseif($condition instanceof Where) {
                $condition = $condition->parse();
            } else {
                throw new Exception('Condition type is incorrect');
            }
        }

        if (is_string($model)) {
            $model = new $model;
        }
        if (
            !($model instanceof Query) &&
            !($model instanceof Model)
        ) {
            throw new Exception('Model type is incorrect');
        }
        $model =  $model->where($condition);
        $count = $model->count($model->getPk());
        $items = $model->page(...$this->getPageParam())->order($order ?: 'id desc')->select();
        // 过滤器
        $this->_callback($filter, 'page_filter', [&$items]);
        $this->returnPage($count, $items->toArray());
    }

    /**
     * 快捷更新
     * @access  protected
     * @param   string|Model            $model      模型
     * @param   array                   $data       更新的数据
     * @param   array|callable          $condition  额外更新条件
     * @param   string                  $pk         主键
     * @param   callable                $after      后置处理
     * @throws  HttpResponseException
     */
    protected function _save(string|Model $model, array $data = [], array|callable $condition = null, string $pk = null, callable $after = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }
        if (!($model instanceof Model)) {
            throw new Exception('Model type is incorrect');
        }

        $pk = $pk ?: $model->getPk();

        // 启动事务
        Db::startTrans();
        try {
            $pkData = $this->request->post($pk, []);
            $model = $model::update($data, function($query) use($pk, $condition) {
                $query->whereIn($pk, $this->request->post($pk, []));
                if (!empty($condition)) {
                    if (is_array($condition)) {
                        $query->where($condition);
                    } elseif(is_callable($condition)) {
                        call_user_func_array($condition, [&$query]);
                    }
                }
            });
            $model->$pk = $pkData;
            // 后置处理
            $afterRes = $this->_callback($after, 'save_after', [&$model]);
            if ($afterRes !== true && !is_null($afterRes)) {
                throw new Exception(is_null($afterRes) ? Code::ACTION_FAIL->value : $afterRes);
            }
            // 提交事务
            Db::commit();
        } catch (\Throwable $th) {
            // 回滚事务
            Db::rollback();
            $this->error(Code::ACTION_FAIL, $th->getMessage());
        }
        $this->success();
    }
    /**
     * 快捷删除
     * @access  protected
     * @param   string|Model            $model      模型
     * @param   bool                    $force      是否强制删除(只有软删除生效)
     * @param   string                  $pk         主键
     * @param   callable                $before     前置处理
     * @param   callable                $after      后置处理
     * @throws  HttpResponseException
     */
    protected function _delete(string|Model $model, bool $force = false, string $pk = null, callable $before = null, callable $after = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }
        if(!($model instanceof BasicModel)) {
            throw new Exception('Model type is incorrect');
        }
        $pk = $pk ?: $model->getPk();
        $condition = $this->request->post($pk, []);
        // 启动事务
        Db::startTrans();
        try {
            // 前置处理
            $beforeRes = $this->_callback($before, 'delete_before', [$pk, &$condition]);
            if ($beforeRes !== true && !is_null($beforeRes)) {
                throw new Exception(is_null($beforeRes) ? Code::ACTION_FAIL->value : $beforeRes);
            }

            // 删除数据
            $model::destroy(function($query) use($pk, $condition){
                $query->whereIn($pk, $condition);
            }, $force);
            // 后置操作
            $afterRes = $this->_callback($after, 'delete_after', [&$model]);
            if ($afterRes !== true && !is_null($afterRes)) {
                throw new Exception(is_null($afterRes) ? Code::ACTION_FAIL->value : $afterRes);
            }

            // 提交事务
            Db::commit();
        } catch (\Throwable $th) {
            // 回滚事务
            Db::rollback();
            $this->error(Code::ACTION_FAIL, $th->getMessage());
        }
        $this->success();
    }
    /**
     * 快捷表单
     * @access  protected
     * @param   string|Model            $model      模型
     * @param   string|Validate         $validate   验证器（名称.场景）
     * @param   array                   $allowField 允许字段
     * @param   string                  $pk         主键
     * @param   callable                $before     写入前置操作
     * @param   callable                $after      写入后置操作
     * @throws  HttpResponseException
     */
    protected function _form(string|Model $model, string|Validate $validate = null, array $allowField = [], string $pk = null, callable $before = null, callable $after = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }
        if (!($model instanceof Model)) {
            throw new Exception('Model type is incorrect');
        }

        $pk = $pk ?: $model->getPk();

        $data = $this->request->post();
            // 验证数据
            if (!empty($validate)) {
                if (is_string($validate)) {
                    @list($validate, $scene) = explode('.', $validate);
                    $validate = new $validate;

                    if (!empty($scene)) $validate->scene($scene);
                } elseif (is_array($validate)) {
                    $validate = FacadeValidate::rule($validate);
                } elseif($validate instanceof Validate) {
                } else {
                    throw new Exception('Validate type is incorrect');
                }

                if (!$validate->check($data)) $this->error(Code::PARAM_ERROR, $validate->getError());
            }

            // 启动事务
            Db::startTrans();
            try {
                // 前置操作
                $beforeRes = $this->_callback($before, 'form_before', [&$data]);
                if ($beforeRes !== true && !is_null($beforeRes)) {
                    throw new Exception(is_null($beforeRes) ? Code::ACTION_FAIL->value : $beforeRes);
                }
                // 主键数据为空，写入数据
                if (empty($data[$pk])) {
                    $model = $model::create($data, $allowField);
                } else { // 修改数据
                    $model = $model::update(array_diff_key($data, [$pk => null]), [$pk => $data[$pk]], $allowField);
                    $model->$pk = $data[$pk];
                }
                // 后置操作
                $afterRes = $this->_callback($after, 'form_after', [&$model, &$data]);
                if ($afterRes !== true && !is_null($afterRes)) {
                    throw new Exception(is_null($afterRes) ? Code::ACTION_FAIL->value : $afterRes);
                }

                // 提交事务
                Db::commit();
            } catch (\Throwable $th) {
                // 回滚事务
                Db::rollback();
                var_dump($th->__toString());
                $this->error(Code::ACTION_FAIL, $th->getMessage());
            }
            $this->success();
    }

    /**
     * 快捷详情
     * @access  protected
     * @param   string|Query|Model     $model      操作模型
     * @param   callable               $filter     过滤器
     * @param   string                 $pk         主键
     * @throws  HttpResponseException
     */
    protected function _detail(string|Query|Model $model, callable $filter = null, string $pk = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }
        $pk = $pk ?: $model->getPk();
        $data = $model->where($pk, $this->request->get($pk))->find();
        
        if (empty($data)) $this->error(Code::DATA_NOT_EXIST);

        $this->_callback($filter, 'detail_filter', [&$data]);

        $this->returnMap($data->toArray());
    }

    /**
     * 快捷恢复
     * @access  protected
     * @param   string|\think\Model     $model      模型
     * @param   string                  $pk         主键
     * @throws  \think\exception\HttpResponseException
     */
    protected function _restore($model, string $pk = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }
        if (!($model instanceof Model)) {
            throw new Exception('Model type is incorrect');
        }

        $pk = $pk ?: $model->getPk();

        // 启动事务
        Db::startTrans();
        try {
            $model->restore([
                [$pk, 'in', $this->request->post($pk, [])],
            ]);
            // 提交事务
            Db::commit();
        } catch (\Throwable $th) {
            // 回滚事务
            Db::rollback();
            $this->error(Code::ACTION_FAIL, $this->app->isDebug() ? "操作失败: {$th->getMessage()}" : '');
        }
        $this->success();
    }
}