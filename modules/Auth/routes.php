<?php

use Common\Helpers\Functions;
use App\Routing\Auth\Route;
use Auth\Controllers\AuthController;
use Auth\Middlewares\AuthMiddleware;

require_once Functions::root('@/modules/Auth/vendor/autoload.php');

Route::register(function () {
    Route::group('api', '/api/', [], function () {
        Route::group('admin', '/admin/', [], function () {
            Route::group('auth', '/auth/', [], function () {
                $controller = AuthController::class;

                Route::post('login', [$controller, 'login']);

                Route::head('check', [$controller, 'check']);

                Route::group('', '', [[AuthMiddleware::class, 'process']], function () use ($controller) {
                    Route::post('register', [$controller, 'register']);
                    Route::post('logout', [$controller, 'logout']);
                });
            });
        });
    });
});
