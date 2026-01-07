<?php

namespace JacquesTredoux\VueSidebarMenu;

use Illuminate\Support\ServiceProvider;
use JacquesTredoux\VueSidebarMenu\Services\MenuWebService;
use JacquesTredoux\VueSidebarMenu\Console\InstallCommand;

class VueSidebarMenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/vue-sidebar-menu.php',
            'vue-sidebar-menu'
        );

        // Register MenuWebService as singleton
        $this->app->singleton(MenuWebService::class, function ($app) {
            return new MenuWebService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'vue-sidebar-menu-migrations');

        // Publish Vue components
        $this->publishes([
            __DIR__.'/../resources/js/Components/SidebarMenu' => resource_path('js/Components/SidebarMenu'),
        ], 'vue-sidebar-menu-components');

        // Publish config file
        $this->publishes([
            __DIR__.'/../config/vue-sidebar-menu.php' => config_path('vue-sidebar-menu.php'),
        ], 'vue-sidebar-menu-config');

        // Register install command
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}

