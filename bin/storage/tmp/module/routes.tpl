<?php

use App\Routing\Auth\Route;
use Modules\Auth\Controllers\AuthController;

Route::register(function () {
    Route::group('api', '/api/', [], function () {
        Route::group('admin', 'admin/', [], function () {
            Route::group('auth', 'auth/', [], function () {
                Route::get('register', [AuthController::class, 'login']);
            });
        });
    });
});
