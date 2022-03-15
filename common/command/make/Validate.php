<?php

namespace command\make;

use think\facade\Db;
use think\helper\Str;

/**
 * 创建验证器
 */
class Validate extends Basic
{
    protected function configure()
    {
        parent::configure();
        $this->setName('make:validate')
            ->setDescription('Create a new validate class');
    }

    /**
     * 获取命名空间
     * @access  protected
     * @return  string
     */
    protected function getNamespace(): string
    {
        return 'validate';
    }

    /**
     * 获取变量
     * @access  protected
     * @param   string  $name   类名
     * @return  array
     */
    protected function getVariable(string $name): array
    {
        $databaseConnections = Db::getConfig('default');
        $database = Db::getConfig("connections.{$databaseConnections}.database");
        $columns = Db::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', Str::snake($name))
            ->whereNotIn('COLUMN_NAME', ['create_time', 'status', 'delete_time'])
            ->column('COLUMN_NAME');
        return [
            'columns' => $columns
        ];
    }

    /**
     * 获取模板文件路径
     * @access protected
     * @return  string
     */
    protected function getTpl(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'tpls' . DIRECTORY_SEPARATOR . 'validate.tpl';
    }
    /**
     * 获取文件路径
     * @access  protected
     * @param   string  $name   类名
     * @return  string
     */
    protected function getPath(string $name): string
    {
        return $this->app->getRootPath() . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'validate' . DIRECTORY_SEPARATOR . $this->getClassName($name) . '.php';
    }
}