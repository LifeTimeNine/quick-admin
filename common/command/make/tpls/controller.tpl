declare (strict_types = 1);

namespace app\{$app_name}\controller;

use model\{$model_name} as {$model_name}Model;
use tools\Query;
use traits\controller\QuickAction;
use validate\{$validate_name} as {$validate_name}Validate;

/**
 * {$title}管理
 */
class {$class_name} extends Basic
{
    use QuickAction;

    /**
     * {$title}列表
     * @menu    true
     * @auth    true
     */
    public function list()
    {
        $query = new Query();
        $this->_page(
            {$model_name}Model::class,
            $query->parse()
        );
    }
    /**
     * 添加{$title}

     * @auth    true
     * @log     true
     */
    public function add()
    {
        $this->_form(
            {$model_name}Model::class,
            {$validate_name}Validate::class . '.add',
            []
        );
    }
    /**
     * {$title}详情
     * @auth    true
     */
    public function detail()
    {
        $this->_detail({$model_name}Model::class);
    }
    /**
     * {$title}角色
     * @auth    true
     * @log     true
     */
    public function edit()
    {
        $this->_form(
            {$model_name}Model::class,
            {$validate_name}Validate::class . '.edit',
            [],
        );
    }
    /**
     * 修改{$title}状态
     * @auth    true
     * @log     true
     */
    public function modifyStatus()
    {
        $this->_save({$model_name}Model::class, [
            'status' => !empty($this->request->post('enable')) ? 1 : 2,
        ]);
    }
    /**
     * 软删除{$title}

     * @auth    true
     * @log     true
     */
    public function softDelete()
    {
        $this->_delete({$model_name}Model::class);
    }
    /**
     * {$title}软删除恢复
     * @auth    true
     * @log     true
     */
    public function restore()
    {
        $this->_restore({$model_name}Model::class);
    }
    /**
     * {$title}真实删除
     * @auth    true
     * @log     true
     */
    public function delete()
    {
        $this->_delete({$model_name}Model::class, true);
    }
}