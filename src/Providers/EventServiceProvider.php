<?php

namespace Larapress\SAzmoon\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // on cart purchased, calculate support share & introducer share
        'Larapress\CRUD\Events\CRUDVerbEvent' => [
            'Larapress\SAzmoon\Services\Azmoon\BuildAzmoonDetailsListener'
        ],
    ];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
