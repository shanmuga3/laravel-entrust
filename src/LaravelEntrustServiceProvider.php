<?php

/**
 * This file is part of Laravel Entrust,
 * Handle Role-based Permissions for Laravel.
 *
 * @license     MIT
 * @package     Shanmuga\LaravelEntrust
 * @category    Provider
 * @author      Shanmugarajan
 */

namespace Shanmuga\LaravelEntrust;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class LaravelEntrustServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/entrust.php' => config_path('entrust.php'),
            __DIR__.'/config/entrust_seeder.php' => config_path('entrust_seeder.php'),
        ],'LaravelEntrust');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-entrust');

        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        $this->registerBladeDirectives();

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeMigrationCommand::class,
                Commands\MakeSeederCommand::class,
                Commands\SetupCommand::class,
                Commands\MakePermissionCommand::class,
                Commands\MakeRoleCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/entrust.php', 'entrust'
        );
        $this->mergeConfigFrom(
            __DIR__.'/config/entrust_seeder.php', 'entrust_seeder'
        );
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        if (!class_exists('\Blade')) {
            return;
        }

        // Call to LaravelEntrust::hasRole
        Blade::directive('role', function($expression) {
            return "<?php if (\\LaravelEntrust::hasRole({$expression})) : ?>";
        });

        Blade::directive('endrole', function($expression) {
            return "<?php endif; // LaravelEntrust::hasRole ?>";
        });

        // Call to LaravelEntrust::can
        Blade::directive('permission', function($expression) {
            return "<?php if (\\LaravelEntrust::can({$expression})) : ?>";
        });

        Blade::directive('endpermission', function($expression) {
            return "<?php endif; // LaravelEntrust::can ?>";
        });

        // Call to LaravelEntrust::ability
        Blade::directive('ability', function($expression) {
            return "<?php if (\\LaravelEntrust::ability({$expression})) : ?>";
        });

        Blade::directive('endability', function($expression) {
            return "<?php endif; // LaravelEntrust::ability ?>";
        });
    }
}