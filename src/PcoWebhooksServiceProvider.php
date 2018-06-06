<?php namespace Smadeira\PcoWebhooks;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PcoWebhooksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/pco-webhooks.php' => config_path('pco-webhooks.php'),
            ], 'config');
        }

        Route::macro('pcoWebhooks', function ($url) {
            return Route::post($url, '\Smadeira\PcoWebhooks\PcoWebhooksController');
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pco-webhooks.php', 'pco-webhooks');
    }
}
