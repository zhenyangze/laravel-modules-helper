<?php

namespace Yangze\ModulesHelper;

use Yangze\ModulesHelper\Console\Providers\ConsoleServiceProvider;
use Yangze\ModulesHelper\Console\Providers\GeneratorServiceProvider;
use Illuminate\Support\ServiceProvider;
use Yangze\ModulesHelper\Lib\HttpCode;

class ModulesHelperServiceProvider extends ServiceProvider
{
    /**
     * @var bool Indicates if loading of the provider is deferred.
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/modules_helper.php' => config_path('modules_helper.php'),
        ], 'config');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/modules_helper.php',
            'modules_helper'
        );

        $this->app->register(GeneratorServiceProvider::class);
    }
}
