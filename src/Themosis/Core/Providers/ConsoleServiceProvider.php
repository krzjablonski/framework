<?php

namespace Themosis\Core\Providers;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Routing\Console\MiddlewareMakeCommand;
use Illuminate\Support\ServiceProvider;
use Themosis\Core\Console\VendorPublishCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Defer the loading of the provider.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Commands to register.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Development commands to register.
     *
     * @var array
     */
    protected $devCommands = [
        'ControllerMake' => 'command.controller.make',
        'MiddlewareMake' => 'command.middleware.make',
        'VendorPublish' => 'command.vendor.publish'
    ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands,
            $this->devCommands
        ));
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            call_user_func_array([$this, "register{$command}Command"], [$commands[$command]]);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the make:controller command.
     *
     * @param string $abstract
     */
    protected function registerControllerMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new ControllerMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:middleware command.
     *
     * @param string $abstract
     */
    protected function registerMiddlewareMakeCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new MiddlewareMakeCommand($app['files']);
        });
    }

    /**
     * Register the vendor:publish command.
     *
     * @param string $abstract
     */
    protected function registerVendorPublishCommand($abstract)
    {
        $this->app->singleton($abstract, function ($app) {
            return new VendorPublishCommand($app['files']);
        });
    }

    /**
     * Return list of services provided.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->commands), array_values($this->devCommands));
    }
}