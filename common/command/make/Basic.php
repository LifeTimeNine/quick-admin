<?php

namespace command\make;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

/**
 * 创建文件基类
 */
abstract class Basic extends Command
{
    protected function configure()
    {
        $this->addArgument('class', Argument::REQUIRED, "The name of the class")
            ->addOption('title', 't', Option::VALUE_OPTIONAL, 'Class annotation title');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('class'));

        $path = $this->getPath($name);

        if (is_file($path)) {
            $output->writeln('<error>' . $this->getNamespace() . '\\' . $name . ' already exists!</error>');
            return false;
        }

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $this->buildClass($name));

        $output->writeln('<info>' . $this->getNamespace() . '\\' . $name . ' created successfully.</info>');

    }

    /**
     * 构建类
     * @access  private
     * @param   string  $name   类名
     * @return  string
     */
    private function buildClass(string $name): string
    {
        $this->app->config->set([
            'tpl_cache' => false
        ], 'view');
        return "<?php" . PHP_EOL . $this->app->view->fetch($this->getTpl(), array_merge(
            [
                'class_name' => $this->getClassName($name),
                'namespace' => $this->getNamespace(),
                'title' => $this->input->getOption('title') ?: $name
            ],
            $this->getVariable($name)
        ));
    }

    /**
     * 获取变量
     * @access  protected
     * @param   string  $name   类名
     * @return  array
     */
    protected function getVariable(string $name): array
    {
        return [];
    }
    /**
     * 获取类名
     * @access  protected
     * @param   string  $name   类名
     * @return  string
     */
    protected function getClassName(string $name): string
    {
        return $name;
    }

    /**
     * 获取命名空间
     * @access  protected
     * @return  string
     */
    abstract protected function getNamespace(): string;

    /**
     * 获取模板文件路径
     * @access protected
     * @return  string
     */
    abstract protected function getTpl(): string;

    /**
     * 获取文件路径
     * @access  protected
     * @param   string  $name   类名
     * @return  string
     */
    abstract protected function getPath(string $name): string;
}