<?php

namespace Yangze\ModulesHelper\Console\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->register();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $generators = [
            'command.make.module.helper.resource' => \Yangze\ModulesHelper\Console\Generators\MakeResourceCommand::class,
            'command.make.module.helper.request' => \Yangze\ModulesHelper\Console\Generators\MakeRequestCommand::class,
            'command.make.module.helper.controller' => \Yangze\ModulesHelper\Console\Generators\MakeControllerCommand::class,
        ];

        foreach ($generators as $slug => $class) {
            $this->app->singleton($slug, function ($app) use ($slug, $class) {
                return $app[$class];
            });

            $this->commands($slug);
        }
    }
}
