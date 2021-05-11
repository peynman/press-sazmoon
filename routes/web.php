<?php

use Illuminate\Support\Facades\Route;
use Larapress\SAzmoon\Services\Azmoon\AzmoonController;

// api routes with public access
Route::middleware(config('larapress.pages.middleware'))
    ->prefix(config('larapress.pages.prefix'))
    ->group(function () {
        AzmoonController::registerPublicWebRoutes();
    });
