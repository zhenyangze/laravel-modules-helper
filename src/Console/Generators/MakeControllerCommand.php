<?php

namespace Yangze\ModulesHelper\Console\Generators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Console\Command;

class MakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:api
    	{slug : The slug of the module}
    	{name : The name of the class}
    	{--model= : the model class}';

    protected $configList = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module Controller class with Request and Resource';

    public function handle()
    {
        if (!$this->modelExists()) {
            $this->error('Model does not exists !');

            return false;
        }
        $modelName = $this->option('model');

        if (strstr($this->argument('name'), 'Controller')) {
            $this->error('class no need to suffix with Controller');
            return false;
        }

        $this->configList = config('modules_helper.files');
        $this->configList[] = [
            'name'  => 'model',
            'path'  => str_ireplace('\\', '/',  $this->option('model')),
            'tag' => '',
        ];

        // 生成文件
        foreach ($this->configList as $stubInfo) {
            $stub = $stubInfo['tag'];
            $file = $stubInfo['path'];
            if (empty($stub)) {
                continue;
            }

            $stubFile = __DIR__ . '/stubs/' . $stub . '.stub';
            if (!file_exists($stubFile)) {
                $this->info('Template File not exists: ' . $stubFile);
                continue;
            }
            $newFile = $this->getNewFile($file);
            if (file_exists(base_path($newFile))) {
                $this->info('Generate File exists: ' . $newFile);
                continue;
            }
            $stubContent = file_get_contents($stubFile);
            $stubContent = $this->replaceClass($stubContent, $newFile);

            // 如果目录不存在，先创建目录
            if (!is_dir(dirname(base_path($newFile)))) {
                mkdir(dirname(base_path($newFile)), 0777, true);
            }
            file_put_contents(base_path($newFile), $stubContent);
        }

        $this->info("\n\n添加成功，请手动添加路由:\n Route::resource('" . implode('/', [
            $this->argument('slug'),
            lcfirst($this->getBaseClassName()),
        ]) . "', '" . str_replace("/", "\\", $this->argument('name')) . "Controller');");
    }

    protected function getBaseClassName()
    {
        return str_replace([
            'Controller',
            'Request',
            'Resource'
        ], '', class_basename($this->argument('name')));
    }

    /**
     * Determine if the model is exists.
     *
     * @return bool
     */
    protected function modelExists()
    {
        $model = $this->option('model');

        if (empty($model)) {
            return false;
        }

        return class_exists($model) && is_subclass_of($model, Model::class);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $newFile
     *
     * @return string
     */
    protected function replaceClass($stub, $newFile)
    {
        $className = str_replace('/', '\\', str_replace('.php', '', basename($newFile)));
        $spaceName = str_replace('/', '\\', dirname($newFile));

        return str_replace(
            [
                'DummyBaseControllerNameSpace',
                'DummyBaseController',
                'DummyNamespace',
                'DummyClass',
                'DummyModelNameSpace',
                'DummyModelClassName',
                'DummyResourceNameSpace',
                'DummyResource',
                'DummyStoreRequestNameSpace',
                'DummyStoreRequest',
                'DummyUpdateRequestNameSpace',
                'DummyUpdateRequest',
                'DummyDestoryRequestNameSpace',
                'DummyDestroyRequest',
            ],
            [
                $this->getNamespaceOrClassName('base', 'namespace'),
                $this->getNamespaceOrClassName('base', 'classname'),
                $spaceName,
                $className,
                $this->getNamespaceOrClassName('model', 'namespace'),
                $this->getNamespaceOrClassName('model', 'classname'),

                $this->getNamespaceOrClassName('resource', 'namespace'),
                $this->getNamespaceOrClassName('resource', 'classname'),
                $this->getNamespaceOrClassName('store', 'namespace'),
                $this->getNamespaceOrClassName('store', 'classname'),
                $this->getNamespaceOrClassName('update', 'namespace'),
                $this->getNamespaceOrClassName('update', 'classname'),
                $this->getNamespaceOrClassName('destory', 'namespace'),
                $this->getNamespaceOrClassName('destory', 'classname'),
            ],
            $stub
        );
    }

    protected function getNewFile($file)
    {
        return str_ireplace([
            '{{module}}',
            '{{class}}'
        ], [
            $this->argument('slug'),
            $this->argument('name'),
        ], $file);
    }

    function getNamespaceOrClassName($name, $getType)
    {
        $path = '';
        foreach ($this->configList as $item) {
            if ($item['name'] == $name) {
                $path = $item['path'];
                break;
            }
        }

        $path = str_replace('{{module}}', $this->argument('slug'), $path);
        $path = str_replace('{{class}}', $this->argument('name'), $path);

        $pathParts = pathinfo($path);
        $filename = $pathParts['filename'];

        if ($getType == 'namespace') {
            $namespace = str_replace('/', '\\', $pathParts['dirname']) . '\\' . $filename;
            return $namespace;
        } else if ($getType == 'classname') {
            return $filename;
        }
    }
}
