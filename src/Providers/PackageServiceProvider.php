<?php

namespace Larapress\SAzmoon\Providers;

use Illuminate\Support\ServiceProvider;
use Larapress\SAzmoon\Commands\SAzmoonCreateProductType;
use Larapress\SAzmoon\Services\Azmoon\AzmoonService;
use Larapress\SAzmoon\Services\Azmoon\IAzmoonService;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IAzmoonService::class, AzmoonService::class);

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'larapress');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        $this->publishes([
            __DIR__.'/../../config/sazmoon.php' => config_path('larapress/sazmoon.php'),
        ], ['config', 'larapress', 'larapress-sazmoon']);

        if ($this->app->runningInConsole()) {
            $this->commands([
                SAzmoonCreateProductType::class,
            ]);
        }
    }
}
