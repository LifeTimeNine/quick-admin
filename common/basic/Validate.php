<?php

namespace basic;

use think\exception\ClassNotFoundException;

/**
 * 验证器基类
 */
class Validate extends \think\Validate
{
    /**
     * 当前验证器关联的模型名称
     * @var string
     */
    protected $model;
    /**
     * 模型默认命名空间
     * @var string
     */
    protected $modelNamespace = 'model';

    /**
     * 验证器默认命名空间
     * @var string
     */
    protected $validateNamespace = 'validate';

    /**
     * 构造方法
     */
    public function __construct()
    {
        parent::__construct();
        if (!empty($this->model)) {
            $model = new $this->model;
            if ($this->lang->has($model->getTable())) {
                $this->field = $this->lang->get($model->getTable());
            }
        }
    }


    /**
     * 验证模型是否存在某条记录
     */
    protected function dataExist($value, $rule, $data = [], $fieldName = '')
    {
        @list($modelName, $queryFieldName) = explode(',', $rule);
        if (strpos($modelName, '\\') === false) {
            $modelName = "{$this->modelNamespace}\\{$modelName}";
        }
        if (!class_exists($modelName)) {
            throw new ClassNotFoundException("model {$modelName} does not exist", $modelName);
        }
        $queryFieldName = $queryFieldName?: $fieldName;
        if (empty($modelName::where($queryFieldName, $value)->find())) {
            if (strpos($fieldName, '|')) {
                // 字段|描述 用于指定属性名称
                [$fieldName, $title] = explode('|', $fieldName);
            } else {
                $title = $this->field[$fieldName] ?? $fieldName;
            }
            if (isset($this->message["{$fieldName}." . __FUNCTION__])) {
                return $this->getRuleMsg($fieldName, $title, __FUNCTION__, $rule);
            } else {
                return $this->parseErrorMsg(Variable::DATA_NOT_EXIST, null, $title);
            }
        }
        return true;
    }

    /**
     * 验证数组数据
     */
    protected function arrayItem($value, $rule, $data = [], $fieldName = '')
    {
        @list($valid, $scene) = explode(',', $rule);
        if (strpos($valid, '\\') === false) {
            $valid = "{$this->validateNamespace}\\{$valid}";
        }
        if (!class_exists($valid)) {
            throw new ClassNotFoundException("validate {$valid} does not exist", $valid);
        }
        /** @var \basic\Validate */
        $valid = new $valid;
        if (!empty($scene)) $valid->scene($scene);

        if (strpos($fieldName, '|')) {
            // 字段|描述 用于指定属性名称
            [$fieldName, $title] = explode('|', $fieldName);
        } else {
            $title = $this->field[$fieldName] ?? $fieldName;
        }

        foreach($value as $key => $item) {
            // 直接处理
            if (!$valid->check($item)) {
                $key ++;
                if (isset($this->message["{$fieldName}." . __FUNCTION__])) {
                    return $this->getRuleMsg($fieldName, $title, __FUNCTION__, $rule);
                } else {
                    return "{$title}: [{$key}] {$valid->getError()}";
                }
            }
        }
        return true;
    }
}
