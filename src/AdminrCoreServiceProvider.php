<?php

namespace Devsbuddy\AdminrCore;

use App\Relations\HasOneRelation;
use Devsbuddy\AdminrCore\ViewComposers\MenuComposer;
use Devsbuddy\AdminrCore\ViewComposers\RelationViewComposer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AdminrCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'adminr-core');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/adminr-core.php' => config_path('adminr-core.php'),
            ], 'laravel-config');

            // Publishing the views.
//            $this->publishes([
//                __DIR__.'/../resources/views' => resource_path('views/vendor/adminr-core'),
//            ], 'laravel-views');

            // Publishing assets.
            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('vendor/adminr-core'),
            ], 'laravel-assets');
        }

        // Load helpers file
        if (file_exists(__DIR__ . '/Http/helpers.php')) {
            require_once __DIR__ . '/Http/helpers.php';
        }

        /**
         * Load menus and compose to all views
         */
        View::composer('*', MenuComposer::class);

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/adminr-core.php', 'adminr-core');

        // Register the main class to use with the facade
        $this->app->singleton('adminr-core', function () {
            return new AdminrCore;
        });
    }
}
