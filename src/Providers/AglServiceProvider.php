<?php

namespace ApurbaLabs\AGL\Providers;

use Illuminate\Support\ServiceProvider;

class AglServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/agl.php', 'agl');
        
        // Bind the Governance Manager
        $this->app->singleton('agl', function ($app) {
            return new \ApurbaLabs\AGL\Services\GovernanceManager($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/agl.php' => config_path('agl.php'),
            ], 'agl-config');
        }
    }
}