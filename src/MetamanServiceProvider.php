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
        $this->publishes([
            __DIR__ . '/config/'  => config_path(''),
            __DIR__ . '/Meta.php' => app_path('/Models'),
        ]);
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
