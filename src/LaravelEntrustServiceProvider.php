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
     * The middlewares to be registered.
     *
     * @var array
     */
    protected $middlewares = [
        'role'          => \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustRole::class,
        'permission'    => \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustPermission::class,
        'ability'       => \Shanmuga\LaravelEntrust\Middleware\LaravelEntrustAbility::class,
    ];

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

        if (class_exists('\Blade')) {
            $this->registerBladeDirectives();
        }

        $this->registerMiddlewares();

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

        $this->app->bind('laravel_entrust', function ($app) {
            return new LaravelEntrust($app);
        });

        $this->app->alias('LaravelEntrust', 'Shanmuga\LaravelEntrust\Facades\LaravelEntrustFacade');
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
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

    /**
     * Register the middlewares automatically.
     *
     * @return void
     */
    protected function registerMiddlewares()
    {
        if (!$this->app['config']->get('entrust.middleware.register')) {
            return;
        }

        $router = $this->app['router'];

        if (method_exists($router, 'middleware')) {
            $registerMethod = 'middleware';
        }
        else if (method_exists($router, 'aliasMiddleware')) {
            $registerMethod = 'aliasMiddleware';
        }
        else {
            return;
        }

        foreach ($this->middlewares as $key => $class) {
            $router->$registerMethod($key, $class);
        }
    }
}