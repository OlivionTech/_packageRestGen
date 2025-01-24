<?php namespace Olivion\RestGen;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RestGenServiceProvider extends ServiceProvider
{
    protected $commands = [
        'RestTest' => 'command.rest.test',
    ];

    /** 
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        
        // $this->mergeConfigFrom(__DIR__.'/../config/rest-gen.php', 'rest-gen');

        // // Register the service the package provides.
        // $this->app->singleton('rest-gen', function ($app) {
        //     return new RestGen;
        // });

        $this->registerCommands($this->commands);
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     */
    protected function registerRestTestCommand()
    {
        $this->app->singleton('command.rest.test', function ($app) {
            return new Console\RestTestCommand();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_values($this->commands);
    }

    /** 
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'olivion');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'olivion');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        // if ($this->app->runningInConsole()) {
        //     $this->bootForConsole();
        // }
    }
}
