<?php

namespace Yangze\ModulesHelper\Console\Generators;

use Caffeinated\Modules\Console\GeneratorCommand;

class MakeWebControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:helper:controller:web
    	{slug : The slug of the module}
    	{name : The name of the resource class}
    	{--model : Generate a module resource class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module Controller class';

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

        return __DIR__.'/stubs/controller-web.stub';
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
        return module_class($this->argument('slug'), 'Http\\Controller');
    }
}
