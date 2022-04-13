
declare (strict_types = 1);

namespace {$namespace};

use basic\{if $is_pivot}Pivot{else/}Model{/if};
{if $has_soft_delete}use think\model\concern\SoftDelete;
{/if}

/**
 * {$title}模型
 */
class {$class_name} extends {if $is_pivot}Pivot{else/}Model{/if}
{
    protected $table = '{$table_name}';
{if !empty($json_field)}

    protected $json = [{:implode(',', $json_field)}];
{/if}
{if $has_soft_delete}

    use SoftDelete;
    protected $deleteTime = 'delete_time';
{/if}
}