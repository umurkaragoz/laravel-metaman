<?php

namespace Umurkaragoz\Metaman;

use Illuminate\Support\ServiceProvider;

class MetamanServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        
        $this->publishes([__DIR__ . '/migrations/' => database_path('migrations')], 'migrations');
        
        $this->publishes([__DIR__ . '/Meta.php' => app_path('Models/Meta.php')], 'models');
        
        $this->publishes([__DIR__ . '/FeedsMeta.php' => app_path('Traits/FeedsMeta.php')], 'traits');
        
        $this->publishes([__DIR__ . '/config/' => config_path('')], 'config');
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
