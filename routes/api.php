<?php

use Illuminate\Support\Facades\Route;
use Larapress\SAzmoon\Services\Azmoon\AzmoonController;

// api routes with public access
Route::middleware(config('larapress.crud.publuc-middlewares'))
    ->prefix(config('larapress.crud.prefix'))
    ->group(function () {
        AzmoonController::registerPublicAPIRoutes();
    });
