<?php

namespace command\make;

use think\console\input\Option;
use think\helper\Str;

/**
 * 创建模型
 */
class Model extends Basic
{
    protected function configure()
    {
        parent::configure();
        $this->setName('make:model')
            ->addOption('softDelete', 's', Option::VALUE_NONE, 'Enable model soft deletion')
            ->addOption('pivot', 'p', Option::VALUE_NONE, 'Is it an intermediate table model')
            ->addOption('json', 'j', Option::VALUE_OPTIONAL, 'JSON field list')
            ->setDescription('Create a new model class');
    }

    /**
     * 获取命名空间
     * @access  protected
     * @return  string
     */
    protected function getNamespace(): string
    {
        return 'model';
    }

    /**
     * 获取变量
     * @access  protected
     * @param   string  $name   类名
     * @return  array
     */
    protected function getVariable(string $name): array
    {
        if (!empty($this->input->getOption('json'))) {
            $jsonField = explode(',', $this->input->getOption('json'));
            foreach ($jsonField as &$item) $item = "'{$item}'";
        } else {
            $jsonField = [];
        }
        return [
            'table_name' => Str::snake($name),
            'has_soft_delete' => $this->input->hasOption('softDelete'),
            'is_pivot' => $this->input->hasOption('pivot'),
            'json_field' => $jsonField
        ];
    }

    /**
     * 获取模板文件路径
     * @access protected
     * @return  string
     */
    protected function getTpl(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'tpls' . DIRECTORY_SEPARATOR . 'model.tpl';
    }
    /**
     * 获取文件路径
     * @access  protected
     * @param   string  $name   类名
     * @return  string
     */
    protected function getPath(string $name): string
    {
        return $this->app->getRootPath() . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . $this->getClassName($name) . '.php';
    }
}