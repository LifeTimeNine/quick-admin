declare (strict_types = 1);

namespace {$namespace};

use basic\Model;
{if $has_soft_delete}use think\model\concern\SoftDelete;
{/if}

/**
 * {$title}

 */
class {$class_name} extends Model
{
    protected $pk = 'id';
    protected $table = '{$table_name}';

    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';
    protected $updateTime = false;
{if $has_soft_delete}

    use SoftDelete;
    protected $deleteTime = 'delete_time';
{/if}
}