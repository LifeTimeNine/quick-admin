<?php

namespace tools;

/**
 * 查询参数处理器
 */
class Query extends \think\db\Where
{
    /**
     * 当前请求信息
     * @var \think\Request
     */
    protected $request;

    /**
     * 创建一个查询参数处理器
     *
     * @param  array    $where      查询条件数组
     * @param  bool     $enclose    是否增加括号
     */
    public function __construct(array $where = [], bool $enclose = false)
    {
        parent::__construct($where, $enclose);
        $this->request = request();
    }

    /**
     * 获取排序规则
     * @access  public
     * @param   string|array    $fields     允许排序的字段
     * @param   string          $queryName  参数名称
     * @param   string          $type       处理方法
     * @param   string          $alias      别名分隔符
     * @param   string          $separator  排序参数分割符
     * @return  string
     */
    public function sortRule($fields, string $queryName = 'sort_rule', string $type = 'get', string $alias = ':', string $separator = '.')
    {
        $fieldRules = [];
        foreach(is_array($fields)?$fields:explode(',', $fields) as $field) {
            $dk = $qk = $field;
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            $fieldRules[$qk] = $dk;
        }
        $rules = [];
        foreach(explode(',', call_user_func([$this->request, $type], $queryName, '')) as $rule) {
            if (empty($rule)) continue;
            [$field, $sortRule] = explode($separator, $rule);
            if (array_key_exists($field, $fieldRules) && in_array($sortRule, ['asc', 'desc'])) {
                $rules[$fieldRules[$field]] = $sortRule;
            }
        }
        return $rules;
    }

    /**
     * 解析查询参数
     * @access  public
     * @param   array|string    $fields     查询字段
     * @param   callable        $callable   处理方法
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function parseParam($fields, callable $callable, string $type = 'get', string $alias = ':')
    {
        foreach(is_array($fields)?$fields:explode(',', $fields) as $field) {
            $dk = $qk = $field;
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if ($this->request->has($qk, $type, true)) {
                if (is_callable($callable)) {
                    call_user_func($callable, $dk, call_user_func([$this->request, $type], $qk), $this);
                }
            }
        }
        return $this;
    }

    /**
     * 追加查询条件
     * @access  public
     * @param   string  $field      查询字段
     * @param   string  $condition  条件
     * @param   mixed   $value      值
     * @return  $this
     */
    public function append(string $field, string $condition, $value)
    {
        $this->where[$field] = [$condition, $value];
        return $this;
    }

    /**
     * 快捷 like 查询
     * @access  public
     * @param   array|string    $fields     查询的字段
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function like($fields, string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) {
            $this->append($dk, 'like', "%{$value}%");
        }, $type, $alias);
        return $this;
    }
    /**
     * 快捷 notLike 查询
     * @param   array|string    $fields     查询的字段
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function notLike($fields, string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) {
            $this->append($dk, 'notLike', "%{$value}%");
        }, $type, $alias);
        return $this;
    }
    /**
     * 快捷 in 查询
     * @param   array|string    $fields     查询的字段
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function in($fields, string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) {
            $this->append($dk, 'in', $value);
        }, $type, $alias);
        return $this;
    }
    /**
     * 快捷 notIn 查询
     * @param   array|string    $fields     查询的字段
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function notIn($fields, string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) {
            $this->append($dk, 'notIn', $value);
        }, $type, $alias);
        return $this;
    }
    /**
     * 快捷 = 查询
     * @param   array|string    $fields     查询的字段
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function equal($fields, string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) {
            $this->append($dk, '=', $value);
        }, $type, $alias);
        return $this;
    }
    /**
     * 快捷 <> 查询
     * @param   array|string    $fields     查询的字段
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function notEqual($fields, string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) {
            $this->append($dk, '<>', $value);
        }, $type, $alias);
        return $this;
    }
    /**
     * 快捷 between 查询
     * @param   array|string    $fields     查询的字段
     * @param   string          $split      输入分割符
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function between($fields, string $split = ' - ', string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) use($split){
            $this->append($dk, 'between', explode($split, $value));
        }, $type, $alias);
        return $this;
    }
    /**
     * 快捷 notBetween 查询
     * @param   array|string    $fields     查询的字段
     * @param   string          $split      输入分割符
     * @param   string          $type       输入类型
     * @param   string          $alias      别名分隔符
     * @return  $this
     */
    public function notBetween($fields, string $split = ' - ', string $type = 'get', string $alias = ':')
    {
        $this->parseParam($fields, function($dk, $value) use($split){
            $this->append($dk, 'notBetween', explode($split, $value));
        }, $type, $alias);
        return $this;
    }
}