declare (strict_types = 1);

namespace {$namespace};

use basic\Validate;

/**
 * {$title}验证器
 */
class {$class_name} extends Validate
{
    protected $rule = [
{foreach $columns as $column}
        '{$column}' => '{if $column == 'id'}require{/if}',
{/foreach}
    ];

{if in_array('id', $columns)}
    protected $message = [
        'id.require' => '请输入ID',
    ];
{else /}
    protected $message = [];
{/if}

    protected function sceneAdd()
    {
        return $this->remove('id', true);
    }

    protected function sceneEdit()
    {
        return $this;
    }
}