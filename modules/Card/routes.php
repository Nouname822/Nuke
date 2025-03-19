<?php

use Common\Helpers\Functions;
use App\Routing\Auth\Route;
use Auth\Middlewares\AuthMiddleware;
use Card\Controllers\CardController;

require_once Functions::root('@/modules/Card/vendor/autoload.php');

Route::register(function () {
    Route::group('api', '/api/', [], function () {
        Route::group('admin', '/admin/', [], function () {
            Route::group('card', '/cards/', [[AuthMiddleware::class, 'process']], function () {
                $controller = CardController::class;
                Route::post('add', [$controller, 'add']);
                Route::put('set/{id}', [$controller, 'set']);
                Route::delete('del/{id}', [$controller, 'del']);
                Route::post('recovery/{id}', [$controller, 'recovery']);
                Route::get('get/{id}', [$controller, 'get']);
            });
        });
    });
});
