<?php

namespace Yangze\ModulesHelper\Console\Generators;

use Caffeinated\Modules\Console\GeneratorCommand;
use Yangze\ModulesHelper\Lib\ResourceGenerator;
use Illuminate\Database\Eloquent\Model;
use Artisan;

class MakeControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:helper:controller
    	{slug : The slug of the module}
    	{name : The name of the resource class}
    	{--model= : Generate a module resource class}
    	{--request : Generate a module request class}
    	{--resource : Generate a module resource class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module Controller class';


    protected $blackFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * $generator 
     */
    protected $generator;
    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Module Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        //if ($this->option('collection')) {
            //return __DIR__.'/stubs/controller-api.stub';
        //}

        return __DIR__.'/stubs/controller-api.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return module_class($this->argument('slug'), 'Http\\Controllers');
    }
    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getRequestNamespace()
    {
        return module_class($this->argument('slug'), 'Http\\Requests');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getResourceNamespace()
    {
        return module_class($this->argument('slug'), 'Http\\Resources');
    }



    public function handle()
    {
        if (!$this->modelExists()) {
            $this->error('Model does not exists !');

            return false;
        }
        $modelName = $this->option('model');
        if (!strstr($this->argument('name'), 'Controller')) {
            $this->error('class need to suffix with Controller');
            return false;
        }

        $this->generator = new ResourceGenerator($modelName);

        $this->generateFiles();

        parent::handle();
    }

    protected function generateFiles()
    {
        // 生成资源文件
        if ($this->option('resource')) {
            Artisan::call('make:module:helper:resource', [
                'slug' => $this->argument('slug'),
                'name' => $this->getTransferName('Resource'),
            ]);
            $this->info('Resource created successfully.');
        }
        if ($this->option('request')) {
            $prefixList = [
                'Create',
                'Update',
                'Delete',
            ];
            foreach($prefixList as $preStr) {
                Artisan::call('make:module:helper:request', [
                    'slug' => $this->argument('slug'),
                    'name' => $this->getTransferName('Request', $preStr),
                ]);
            }
            $this->info('Request created successfully.');
        }
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
            return true;
        }

        return class_exists($model) && is_subclass_of($model, Model::class);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace(
            [
                'DummyModelNameSpace',
                'DummyModelClassName', // 小写前缀
                'DummyModelClass',

                'DummyResourceNameSpace',
                'DummyResource',

                'DummyCreateRequestNameSpace',
                'DummyCreateRequest',

                'DummyUpdateRequestNameSpace',
                'DummyUpdateRequest',

                'DummyDeleteRequestNameSpace',
                'DummyDeleteRequest',

                'DummyFields',
            ],
            [
                $this->option('model'),
                '$' . lcfirst(class_basename($this->option('model'))),
                class_basename($this->option('model')),

                $this->getTransferNameSpace('Resource'),
                class_basename($this->getTransferNameSpace('Resource')),

                $this->getTransferNameSpace('Request', 'Create'),
                class_basename($this->getTransferNameSpace('Request', 'Create')),

                $this->getTransferNameSpace('Request', 'Update'),
                class_basename($this->getTransferNameSpace('Request', 'Update')),
                $this->getTransferNameSpace('Request', 'Delete'),
                class_basename($this->getTransferNameSpace('Request', 'Delete')),

                $this->generateFillField(),
            ],
            $stub
        );
    }

    protected function getTransferName($type, $preStr = '')
    {
        $name = $this->argument('name');
        return str_replace('Controller', $preStr . $type, $name);
    }

    protected function getTransferNameSpace($type, $preStr = '') {
        $newName = $this->getTransferName($type, $preStr);
        $method = 'get' . $type . 'Namespace';
        $newsNameSpace = $this->$method() . '\\' . $newName;
        $newsNameSpace = str_replace('/', '\\', $newsNameSpace);
            
        return $newsNameSpace;
    }

    protected function generateFillField()
    {
        $fields = [];
        foreach($this->generator->getTableColumns() as $column) {
            $columnName = $column->getName();
            if (in_array($columnName, $this->blackFields)) {
                continue;
            }
            $fields[] = '\'' . $columnName . '\'';
        }

        return '[' . implode(',', $fields) . ']';
    }
}
